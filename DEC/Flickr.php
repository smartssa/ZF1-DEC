<?php
/**
 * DEC_Flickr
 */

require_once 'DEC/Rest.php';

class DEC_Flickr extends DEC_Rest
{
    protected $flickrUrl = 'http://api.flickr.com/services/rest/';
    protected $flickrApiKey = '222331b8dc95c1f353ec4d482042b208';
    protected $flickrSecret = '0e98066710a2b25e';

    public function __construct()
    {
        $this->setBaseUrl($this->flickrUrl);
        $this->setApiKey($this->flickrApiKey);
        $this->setApiSecret($this->flickrSecret);
        $this->setMode('flickr');
        //        $this->defaultOptions = array('format' => 'json');
    }

    public function callComplete($result) {
        // nothing here
    }

    public function setupApi($args) {
        // nothing here
    }

    public function testEcho($args)
    {
        return $this->call('flickr.test.echo', $args);
    }

    public function generateToken($secret, $args)
    {
        // no signature for flickr
        return '';
    }

    public function searchByTags($tags, $tag_mode = 'any')
    {
        $args['tags']     = $tags;
        $args['tag_mode'] = $tag_mode;
        
        return $this->photosSearch($args);
    }

    //    activity
    //
    //    * flickr.activity.userComments
    //    * flickr.activity.userPhotos
    //
    //auth
    //
    //    * flickr.auth.checkToken
    //    * flickr.auth.getFrob
    //    * flickr.auth.getFullToken
    //    * flickr.auth.getToken
    //
    //blogs
    //
    //    * flickr.blogs.getList
    //    * flickr.blogs.getServices
    //    * flickr.blogs.postPhoto
    //
    //collections
    //
    //    * flickr.collections.getInfo
    //    * flickr.collections.getTree
    //
    //commons
    //
    //    * flickr.commons.getInstitutions
    //
    //contacts
    //
    //    * flickr.contacts.getList
    //    * flickr.contacts.getListRecentlyUploaded
    //    * flickr.contacts.getPublicList
    //
    //favorites
    //
    //    * flickr.favorites.add
    //    * flickr.favorites.getList
    //    * flickr.favorites.getPublicList
    //    * flickr.favorites.remove
    //
    //groups
    //
    //    * flickr.groups.browse
    //    * flickr.groups.getInfo
    //    * flickr.groups.search
    //
    //groups.members
    //
    //    * flickr.groups.members.getList
    //
    //groups.pools
    //
    //    * flickr.groups.pools.add
    //    * flickr.groups.pools.getContext
    //    * flickr.groups.pools.getGroups

    public function groupsPoolsGetPhotos($args) {
        return new DEC_Flickr_PhotoList($this->call('flickr.groups.pools.getPhotos', $args));
    }

    //    * flickr.groups.pools.remove
    //
    //interestingness
    //
    //    * flickr.interestingness.getList
    //
    //machinetags
    //
    //    * flickr.machinetags.getNamespaces
    //    * flickr.machinetags.getPairs
    //    * flickr.machinetags.getPredicates
    //    * flickr.machinetags.getRecentValues
    //    * flickr.machinetags.getValues
    //
    //panda
    //
    //    * flickr.panda.getList
    //    * flickr.panda.getPhotos
    //
    //people
    //
    //    * flickr.people.findByEmail
    //    * flickr.people.findByUsername
    //    * flickr.people.getInfo
    //    * flickr.people.getPublicGroups
    //    * flickr.people.getPublicPhotos
    //    * flickr.people.getUploadStatus
    //
    //photos
    //
    //    * flickr.photos.addTags
    //    * flickr.photos.delete
    //    * flickr.photos.getAllContexts
    //    * flickr.photos.getContactsPhotos
    //    * flickr.photos.getContactsPublicPhotos
    //    * flickr.photos.getContext
    //    * flickr.photos.getCounts
    //    * flickr.photos.getExif
    //    * flickr.photos.getFavorites
    //    * flickr.photos.getInfo
    //    * flickr.photos.getNotInSet
    //    * flickr.photos.getPerms
    //    * flickr.photos.getRecent
    //    * flickr.photos.getSizes
    //    * flickr.photos.getUntagged
    //    * flickr.photos.getWithGeoData
    //    * flickr.photos.getWithoutGeoData
    //    * flickr.photos.recentlyUpdated
    //    * flickr.photos.removeTag
    //    * flickr.photos.search
    public function photosSearch($args) {
        return new DEC_Flickr_PhotoList($this->call('flickr.photos.search', $args));
    }
    //    * flickr.photos.setContentType
    //    * flickr.photos.setDates
    //    * flickr.photos.setMeta
    //    * flickr.photos.setPerms
    //    * flickr.photos.setSafetyLevel
    //    * flickr.photos.setTags
    //
    //photos.comments
    //
    //    * flickr.photos.comments.addComment
    //    * flickr.photos.comments.deleteComment
    //    * flickr.photos.comments.editComment
    //    * flickr.photos.comments.getList
    //    * flickr.photos.comments.getRecentForContacts
    //
    //photos.geo
    //
    //    * flickr.photos.geo.batchCorrectLocation
    //    * flickr.photos.geo.correctLocation
    //    * flickr.photos.geo.getLocation
    //    * flickr.photos.geo.getPerms
    //    * flickr.photos.geo.photosForLocation
    //    * flickr.photos.geo.removeLocation
    //    * flickr.photos.geo.setContext
    //    * flickr.photos.geo.setLocation
    //    * flickr.photos.geo.setPerms
    //
    //photos.licenses
    //
    //    * flickr.photos.licenses.getInfo
    //    * flickr.photos.licenses.setLicense
    //
    //photos.notes
    //
    //    * flickr.photos.notes.add
    //    * flickr.photos.notes.delete
    //    * flickr.photos.notes.edit
    //
    //photos.transform
    //
    //    * flickr.photos.transform.rotate
    //
    //photos.upload
    //
    //    * flickr.photos.upload.checkTickets
    //
    //photosets
    //
    //    * flickr.photosets.addPhoto
    //    * flickr.photosets.create
    //    * flickr.photosets.delete
    //    * flickr.photosets.editMeta
    //    * flickr.photosets.editPhotos
    //    * flickr.photosets.getContext
    //    * flickr.photosets.getInfo
    //    * flickr.photosets.getList
    //    * flickr.photosets.getPhotos
    //    * flickr.photosets.orderSets
    //    * flickr.photosets.removePhoto
    //
    //photosets.comments
    //
    //    * flickr.photosets.comments.addComment
    //    * flickr.photosets.comments.deleteComment
    //    * flickr.photosets.comments.editComment
    //    * flickr.photosets.comments.getList
    //
    //places
    //
    //    * flickr.places.find
    //    * flickr.places.findByLatLon
    //    * flickr.places.getChildrenWithPhotosPublic
    //    * flickr.places.getInfo
    //    * flickr.places.getInfoByUrl
    //    * flickr.places.getPlaceTypes
    //    * flickr.places.getShapeHistory
    //    * flickr.places.getTopPlacesList
    //    * flickr.places.placesForBoundingBox
    //    * flickr.places.placesForContacts
    //    * flickr.places.placesForTags
    //    * flickr.places.placesForUser
    //    * flickr.places.resolvePlaceId
    //    * flickr.places.resolvePlaceURL
    //    * flickr.places.tagsForPlace
    //
    //prefs
    //
    //    * flickr.prefs.getContentType
    //    * flickr.prefs.getGeoPerms
    //    * flickr.prefs.getHidden
    //    * flickr.prefs.getPrivacy
    //    * flickr.prefs.getSafetyLevel
    //
    //reflection
    //
    //    * flickr.reflection.getMethodInfo
    //    * flickr.reflection.getMethods
    //
    //tags
    //
    //    * flickr.tags.getClusterPhotos
    //    * flickr.tags.getClusters
    //    * flickr.tags.getHotList
    //    * flickr.tags.getListPhoto
    //    * flickr.tags.getListUser
    //    * flickr.tags.getListUserPopular
    //    * flickr.tags.getListUserRaw
    //    * flickr.tags.getRelated
    //
    //test
    //
    //    * flickr.test.echo
    //    * flickr.test.login
    //    * flickr.test.null
    //
    //urls
    //
    //    * flickr.urls.getGroup
    //    * flickr.urls.getUserPhotos
    //    * flickr.urls.getUserProfile
    //    * flickr.urls.lookupGroup
    //    * flickr.urls.lookupUser
    //


}