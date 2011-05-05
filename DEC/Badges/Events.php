<?php 
/**
 * @author dclarke
 *
 */
class DEC_Badges_Events extends DEC_Db_Table {

    protected $_name = 'badges_rules';
    protected $_instance = null;

    const EVENT_CLICK  = 1;
    
    const RULE_COUNTER = 'COUNTER';
    const RULE_ONETIME = 'ONETIME';
    const RULE_HYBRID  = 'HYBRID';
    const RULE_VALUE   = 'VALUE';
    
    public function queueTriggerEvent($userId, $event, $returnBadges = false, $extra = null) {
        
        // attempt to queue the event so the user doesn't get blocked
        // if queue fails, do it immediately
        
    }
    
    public function triggerEvent($userId, $event, $returnBadges = false, $extra = null) {
        // find all rules that are a aprt of this event
        $rules = $this->getRulesForEventType($event); 
        // process each rule
        foreach ($rules as $rule) {
            // process z rules.
            echo $rule->rule_type;
            switch ($rule->rule_type) {
                case self::RULE_COUNTER:
                    // increment the counter and evaluate the rule
                    break;
                case self::RULE_ONETIME:
                    // blip! unlocked, if it hasn't been before.
                    break;
                case self::RULE_HYBRID:
                    // check multiple rules
                    echo sprintf($rule->rule_string, $extra), "<br/>";
                    var_dump(eval(sprintf($rule->rule_string, $extra)));
                    break;
                case self::RULE_VALUE:
                    // uh.. not sure yet.
                    break;
                default:
                    break; 
            }
            // update any counters
        }
        // return a badges object
        if ($returnBadges) {
            return new DEC_Badges($userId);
        }
        return true;

    }
    
    public function getRulesForEventType($eventType) {
        $where = $this->getAdapter()->quoteInto('event_flags = ?', $eventType);
        
        $rowset = $this->fetchAll($where);
        
        return $rowset;
    }
}