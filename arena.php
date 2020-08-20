<?php namespace arena;

//this encodes data into URL format for post statements? ...i think?
function urlEncode($data){
    $output = '';
    $isFirst = true;
    foreach ($data as $key => $value) {
        if(!$isFirst){
            $output = $output.'&';
        }
        $isFirst = false;
        $output = $output.$key.'='.$value;
    }
    return $output;
}
function arrayToList($data){
    $output = '';
    foreach ($data as $value) {
        $output = $output.$value.',';
    }
    return $output;
}

//the Arena class manages the access tokens, bad token handeling must be implemented in endpoints
class Arena{
    private $username = "";
    private $password = "";
    private $apiKey = "";
    private $privateKey = "";
    private $url = "";
    // private $apiSession;
    // private $apiSessionStart;

    public function __construct($url, $username, $password, $apiKey, $privateKey){
        $this->username = $username;
        $this->password = $password;
        $this->apiKey = $apiKey;
        $this->privateKey = $privateKey;
        $this->url = $url;
        //echo 'Arena Initialized';
    }

    //gets n sets
    public function getUsername(){ return $this->username;}
    public function setUsername($username){
        $this->username = $username;
        return true;
    }
    public function getPassword(){ return $this->password;}
    public function setPassword($password){
        $this->password = $password;
        return true;
    }
    public function getApiKey(){ return $this->apiKey;}
    public function setApiKey($apiKey){
        $this->apiKey = $apiKey;
        return true;
    }
    public function getPrivateKey(){ return $this->privateKey;}
    public function setPrivateKey($privateKey){
        $this->privateKey = $privateKey;
        return true;
    }
    public function getUrl(){ return $this->url;}
    public function setUrl($url){
        $this->url = $url;
        return true;
    }

    //NOTE: this makes a new session every call :/ might need to change later
    private function getSession(){
        $data = array('username' => $this->username, 'password' => $this->password, 'api_key' => $this->apiKey);
        //['SessionID']
        $session = $this->post('login', $data, false);
        return $session['SessionID'];
    }

    //gets data from arena
    public function get(String $uri, $sigRequired = true){
        if($sigRequired) $uri = $this->calculateSigniture($uri);
        $uri = $this->url.$uri;
        //echo $uri;
        $options = array(
            CURLOPT_URL => $uri,
            CURLOPT_RETURNTRANSFER => true
        );
        $curl = curl_init();
        curl_setopt_array($curl, $options);

        $result = curl_exec($curl);
        curl_close($curl);
        try {
            $xml = new \SimpleXMLElement($result);
            $json = json_encode($xml);
            $result = json_decode($json, true);
            //echo var_dump($result).'<br>';
            return $result;
        }
        catch( Exception $e ) {
            echo "found an exception: " . $e;
            return false;
        }
    }

    //sends data to arena
    public function post(string $uri, array $data, $sigRequired = true){
        $data = urlEncode($data);
        if($sigRequired) $uri = $this->calculateSigniture($uri);
        $options = array(
            CURLOPT_URL => $this->url.$uri,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_RETURNTRANSFER => true,
        );
        $curl = curl_init();
        curl_setopt_array($curl, $options);


        $result = curl_exec($curl);
        curl_close($curl);
        //this is ugly, but idk how else to do it
        try {
            //echo var_dump($result);
            $xml = new \SimpleXMLElement($result);
            $json = json_encode($xml);
            $result = json_decode($json, true);
            return $result;
        }
        catch( Exception $e ) {
            //echo "found an exception: " . $e;
            return false;
        }
    }

    //calculates signiture for most session calls
    private function calculateSigniture($uri){
        $session = $this->getSession();
        $preHash = $this->privateKey.'_'.$uri.'&api_session='.$session;
        $preHash = strtolower($preHash);
        $sig = md5($preHash);
        $uri = $uri.'&api_session='.$session.'&api_sig='.$sig;
        return $uri;
    }

    //gets the version of Arena
    public function getVersion(){
        $uri = 'version?';
        return $this->get($uri);
    }
}


//arena API function calls
namespace arena\person;
//Details
function getDetails(\arena\Arena $arena, int $personID, array $fields = []){
    $fields = \arena\arrayToList($fields);

    $uri = 'person/'.$personID.'?fields='.$fields;
    $result = $arena->get($uri);
    return $result;
}
function getMyDetails(\arena\Arena $arena, array $fields = []){
    $fields = \arena\arrayToList($fields);
    $uri = 'me?fields='.$fields;
    $result = $arena->get($uri);
    return $result;
}
//list
function getPeople(\arena\Arena $arena, array $criteria, array $fields = [], string $sortField = ''){
    $uri = \arena\urlEncode($criteria);
    $uri = $uri.'&fields='.\arena\arrayToList($fields);
    $uri = 'person/list?'.$uri;
    $uri = $uri.'&sortField='.$sortField;
    $result = $arena->get($uri);
    return $result['Persons']['Person'];
}
//Attributes
function getAttributes(\arena\Arena $arena, int $personID, int $groupID = -1){
    $uri = 'person/'.$personID.'/attribute/list?';
    if($groupID != -1) $uri = $uri.'group='.$groupID;
    echo $uri.'<br>';
    $result = $arena->get($uri);
    return $result['AttributeGroups']['AttributeGroup'];
}
function listAttributes(\arena\Arena $arena, int $groupID = -1){
    $uri = 'attribute/list?';
    if($groupID != -1) $uri = $uri.'group='.$groupID;
    $result = $arena->get($uri);
    return $result['AttributeGroups']['AttributeGroup'];
}
//Groups
function getGroups(\arena\Arena $arena, int $personID, $catagoryID){
    $uri = 'person/'.$personID.'/group/list?'.'categoryid='.$catagoryID;
    $result = $arena->get($uri);
    return $result['Groups']['Group'];
}
function getSubscribedGroups(\arena\Arena $arena, int $personID){
    $uri = 'person/'.$personID.'/group/subscribedlist?';
    $result = $arena->get($uri);
    return $result['Groups'];
}
//Notes
function getNotes(\arena\Arena $arena, int $personID, int $start = -1, int $max = -1, bool $display = false){
    $uri = 'person/'.$personID.'/note/list?';
    if($start != -1) $uri = $uri.'&start='.$start;
    if($max != -1) $uri = $uri.'&max='.$max;
    if($display == true) $uri = $uri.'&display=true';
    $result = $arena->get($uri);
    return $result['Notes']['PersonNote'];
}

namespace arena\family;
function getDetails(\arena\Arena $arena, int $familyID, array $fields = []){
    $fields = \arena\arrayToList($fields);
    $uri = 'family/'.$familyID.'?fields='.$fields;
    $result = $arena->get($uri);
    return $result;
}

namespace arena\security;
function getSecurityObjects(\arena\Arena $arena){
    $uri = 'security/objecttype/list?';
    $result = $arena->get($uri);
    return $result['ObjectTypes']['SecurityObjectType'];
}
function getSecurityTemplates(\arena\Arena $arena, string $type){
    $uri = 'security/template/list?type='.$type;
    $result = $arena->get($uri);
    return $result;
}

namespace arena\group;
//groups
function getDetails(\arena\Arena $arena, int $groupID){
    $uri = 'group/'.$groupID.'?';
    //$uri = $uri.'fields='.\arena\arrayToList($fields);
    $result = $arena->get($uri);
    return $result;
}
function getGroups(\arena\Arena $arena, int $catagoryID, array $criteria = []){
    $uri = 'group/list?categoryid='.$catagoryID;
    //$uri = $uri.'&fields='.\arena\arrayToList($fields);
    $uri = $uri.'&'.\arena\urlEncode($criteria);
    $result = $arena->get($uri);
    return $result['Groups']['Group'];
}
//members
function getMembers(\arena\Arena $arena, int $groupID, int $roleID = -1, array $fields = []){
    $uri = 'group/'.$groupID.'/member/list?';
    $uri = $uri.'fields='.\arena\arrayToList($fields);
    if($roleID != -1) $uri = $uri.'&roleid='.$roleID;
    $result = $arena->get($uri);
    return $result['Members']['GroupMember'];
}
function getMemberDetails(\arena\Arena $arena, int $groupID, int $personID, array $fields = []){
    $uri = 'group/'.$groupID.'/member/'.$personID.'?fields='.\arena\arrayToList($fields);
    $result = $arena->get($uri);
    return $result;
}
//catagories
function getCatagories(\arena\Arena $arena){
    $uri = 'category/list?';
    $result = $arena->get($uri);
    return $result['Categories']['Category'];
}
function getCategorieLeaders(\arena\Arena $arena, int $categoryID, array $fields = []){
    $uri = 'category/'.$categoryID.'/leaderlist?fields='.\arena\arrayToList($fields);
    $result = $arena->get($uri);
    return $result['Members']['GroupMember'];
}

namespace arena\promotion;
function getPromotions(\arena\Arena $arena, int $topicAreaList, string $areaFilter, array $criteria = []){
    $uri = 'promotion/list?topicAreasList='.$topicAreaList.'&areaFilter='.$areaFilter;
    $uri = $uri.'&'.\arena\urlEncode($criteria);
    $result = $arena->get($uri);
    return $result['Promotions'];
}

namespace arena\profile;
function getDetails(\arena\Arena $arena, int $profileID){
    $uri = 'profile/'.$profileID.'?';
    $result = $arena->get($uri);
    return $result;
}
function getProfiles(\arena\Arena $arena, int $profileType){
    $uri = 'profile/list?profiletype='.$profileType;
    $result = $arena->get($uri);
    return $result['Profiles']['Profile'];
}
function getParentProfileList(\arena\Arena $arena, int $profileID){
    $uri = 'profile/'.$profileID.'/list?';
    $result = $arena->get($uri);
    return $result['Profiles']['Profile'];
}
function getMembers(\arena\Arena $arena, int $profileID, array $fields = []){
    $uri = 'profile/'.$profileID.'/member/list?';
    $uri = $uri.'fields='.\arena\arrayToList($fields);
    $result = $arena->get($uri);
    return $result['ProfileMembers']['ProfileMember'];
}
function getMemberDetails(\arena\Arena $arena, int $profileID, int $personID, array $fields = []){
    $uri = 'profile/'.$profileID.'/member/'.$personID.'?';
    $uri = $uri.'fields='.\arena\arrayToList($fields);
    $result = $arena->get($uri);
    return $result;
}
// namespace arena\contribution
//     //batches
//     //NOTE: needs testing
//     public static function getBatchDetails(\arena\Arena $arena, int $batchID){
//         $uri = 'batch/'.$batchID.'?';
//         $result = $arena->get($uri);
//         return $result;
//     }
//     //NOTE: needs testing
//     public static function getBatches(\arena\Arena $arena, array $criteria = []){
//         $uri = 'batch/list?';
//         $uri = $uri.\arena\urlEncode($criteria);
//         //echo $uri;
//         $result = $arena->get($uri);
//         return $result;
//     }
//     //NOTE: needs testing
//     public static function getBatchTypes(\arena\Arena $arena){
//         $uri = 'batchtype/list?';
//         $result = $arena->get($uri);
//         return $result;
//     }
//     //funds
//     //NOTE: needs testing
//     public static function getFundDetails(\arena\Arena $arena, int $fundID){
//         $uri = 'fund/'.$fundID.'?';
//         $result = $arena->get($uri);
//         return $result;
//     }
//     //NOTE: needs testing
//     //NOTE: criteria not listed in API PDF
//     public static function getFunds(\arena\Arena $arena, array $criteria = []){
//         $uri = 'fund/list?';
//         $uri = $uri.\arena\urlEncode($criteria);
//         $result = $arena->get($uri);
//         return $result;
//     }
//     //projects
//     //NOTE: needs testing
//     public static function getProjectDetails(\arena\Arena $arena, int $projectID){
//         $uri = 'project/'.$projectID.'?';
//         $result = $arena->get($uri);
//         return $result;
//     }
//     //NOTE: needs testing
//     public static function getProjects(\arena\Arena $arena){
//         $uri = 'project/list?';
//         $result = $arena->get($uri);
//         return $result;
//     }
//     //contributions
//     //NOTE: needs testing
//     public static function getContributionDetails(\arena\Arena $arena, int $contributionID){
//         $uri = 'contribution/'.$contributionID.'?';
//         $result = $arena->get($uri);
//         return $result;
//     }
//     //NOTE: needs testing
//     public static function getContributions(\arena\Arena $arena, array $criteria = []){
//         $uri = 'contribution/list?';
//         $uri = $uri.\arena\urlEncode($criteria);
//         $result = $arena->get($uri);
//         return $result;
//     }
// }

//TEMPLATE for namespaceing
// namespace arena\person;
// class something{
//     public static function getPeople(\arena\\arena\Arena $arena, array $criteria, array $fields = [], string $sortField = ''){
//         $uri = \arena\urlEncode($criteria);
//         $uri = $uri.'&fields='.\arena\\arena\arrayToList($fields);
//         $uri = 'person/list?'.$uri;
//         $uri = $uri.'&sortField='.$sortField;
//         $result = $arena->get($uri);
//         return $result['Persons']['Person'];
//     }

?>
