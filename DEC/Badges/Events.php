<?php
/**
 * @author dclarke
 *
 */
class DEC_Badges_Events extends DEC_Db_Table {

    protected $_name = 'badges_rules';
    /**
     * Enter description here ...
     * @var DEC_Badges
     */
    protected $_badges = null;
    protected $_instance = null;

    // this is a mysql set, but it returns the string
    const RULE_COUNTER = 'COUNTER';
    const RULE_ONETIME = 'ONETIME';
    const RULE_HYBRID  = 'HYBRID';
    const RULE_VALUE   = 'VALUE';

    public function queueTriggerEvent($userId, $event, $returnBadges = false, $extra = null) {

        // attempt to queue the event so the user doesn't get blocked
        // if queue fails, do it immediately

    }

    public function triggerEvent($userId, $event, $returnBadges = false, $extra = null) {
        // create a badges item thingy ma jigger.
        $this->_badges = new DEC_Badges($userId);
        $dbTally = new DEC_Badges_RulesTally();
        // find all rules that are a aprt of this event
        $rules = $this->getRulesForEventType($event, $userId);
        // process each rule
        $rulestring = null;
        $hybrid 	= array();

        foreach ($rules as $rule) {
            // process z rules.
            switch ($rule->rule_type) {
                case self::RULE_COUNTER:
                    // increment the counter and evaluate the rule
                    // 	update any counters
                    $d = $dbTally->incrementTally($userId, $rule->id);
                    $rulestring = sprintf($rule->rule_string, $d);
                    if (eval($rulestring)) {
                        $this->_badges->unlock($rule->badges_id, $userId);
                    }
                    break;
                case self::RULE_ONETIME:
                    // blip! unlocked, if it hasn't been before.
                    $rulestring = sprintf($rule->rule_string, $extra);
                    if (eval($rulestring)) {
                        $this->_badges->unlock($rule->badges_id, $userId);
                    }
                    break;
                case self::RULE_VALUE:
                    // uh.. not sure yet.
                    break;
                case self::RULE_HYBRID:
                    // check multiple rules
                    if (! isset($hybrid[$rule->badges_id])) {
                        $hybrid[$rule->badges_id] = array();
                    }
                    $rulestring = sprintf($rule->rule_string, $extra);
                    // eval this rule and set a tally for complete for
                    // this particular leg of a hybrid set
                    if (eval($rulestring)) {
                        // eval success, throw it in the db for future checking
                        $d = $dbTally->incrementTally($userId, $rule->id);
                        $hybrid[$rule->badges_id][] = true;
                    } else if ($dbTally->getTally($userId, $rule->id) > 0) {
                        // rule was passed previously
                        $hybrid[$rule->badges_id][] = true;
                    } else {
                        // go away, this rule failed.
                        $hybrid[$rule->badges_id][] = false;
                    }
                    break;
                default:
                    break;
            }
        }

        if (count($hybrid) > 0) {
            // all hybrid rules for this badge must be matched.
            foreach ($hybrid as $badgeId => $hybridRules) {
                $success = true;
                foreach ($hybridRules as $rulePassedForHybrid) {
                    // verify each rule in hybrid is successful
                    $success = $success && $rulePassedForHybrid;
                }
                if ($success) {
                    $this->_badges->unlock($badgeId, $userId);
                }
            }
        }

        // return a badges object
        if ($returnBadges) {
            return new $this->_badges;
        }
        return true;
    }

    public function getRulesForEventType($eventType, $userId) {
        // find only rules that the user has not unlocked
        // get this users unlocked badges
        $usersBadgeIds = $this->_badges->getBadgeIds();
        // also exclude ones that are disabled
        $disabledBadgeIds = $this->_badges->getDisabledIds();
        $excludeIds = array_merge($usersBadgeIds, $disabledBadgeIds);

        $where = array();
        
        $where[] = $this->getAdapter()->quoteInto('event_flags = ?', $eventType);
        if (count($excludeIds) > 0) {
            $where[] = new Zend_Db_Expr('badges_id NOT IN (' . implode(',', $excludeIds) . ')');
        }

        $rowset = $this->fetchAll($where);
        return $rowset;
    }
}