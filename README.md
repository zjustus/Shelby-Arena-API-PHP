<h1>PHP Arena API Library</h1>
<p>Use this library to access the Shelby Arena API in your PHP projects</p>

<h2>How to use</h2>
<ol>
    <li>include arena.php into whatever file your working with</li>
    <li>make a new Arena object <code>$arena = new arena\Arena($url, $username, $password, $apiKey, $privateKey);</code></li>
    <li>call any arena function defined below</li>
</ol>
<h2>Notes</h2>
<p>
    only functions are available right now. classes comming soon<br>
    Only get functions are available right now. Post functions comming soon<br>
    if this is your first use of the Arena API I had an issue with the Access-Control-Allow-Origin. make sure you make the necicary changes to your web.config or web server to allow the api to work
</p>

<h2>Example Code</h2>
<p>
    <code>$arena = new arena\Arena('https://arena.churchwebsite.com/api.svc/', 'userName', 'password', 'API key', API Secret');</code>
    <code>$myDetails = arena\person\getMyDetails($arena);</code>
    <code>echo var_dump($myDetails);</code>
</p>


<h2>Functions</h2>
<ul>
    <li>Namespace: arena
        <ul>
            <li>Class: Arena
                <ul>
                    <li>function __construct($url, $username, $password, $apiKey, $privateKey)</li>
                    <li>function getUsername()</li>
                    <li>function setUsername($username)</li>
                    <li>function getPassword()</li>
                    <li>function setPassword($password)</li>
                    <li>function getApiKey()</li>
                    <li>function setApiKey($apiKey)</li>
                    <li>function getPrivateKey()</li>
                    <li>function setPrivateKey($privateKey)</li>
                    <li>function getUrl()</li>
                    <li>function setUrl($url)</li>
                    <li>function get(String $uri, $sigRequired = true)</li>
                    <li>function post($uri, $data, $sigRequired = true)</li>
                    <li>function getVersion()</li>
                </ul>
            </li>
        </ul>
    </li>
    <li>Namespace: arena\person
        <ul>
            <li>function getDetails(Arena $arena, $personID, array $fields = []) - Gets details about a person</li>
            <li>function getAttributes(Arena $arena, int $personID, int $groupID) - Gets attributes about a person</li>
            <li>function getPeople(Arena $arena, array $criteria, array $fields = []) - Lists people that fir the given criteria, see below</li>
            <li>function listAttributes(Arena $arena, int $groupID = -1) - lists arena attributes</li>
            <li>function getMyDetails(Arena $arena, array $fields = []) - gets details about the user using the API</li>
            <li>function getGroups(Arena $arena, int $personID, $catagoryID) - lists all the groups that user belongs to</li>
            <li>function getSubscribedGroups(Arena $arena, int $personID) - lists all the groups that user is subscribed to</li>
            <li>function getNotes(Arena $arena, int $personID, int $start = -1, int $max = -1, bool $display = false) - gets all the notes about a person</li>
        </ul>
    </li>
    <li>Namespace: arena\family
        <ul>
            <li>function getDetails(Arena $arena, int $familyID) - gets details about a family</li>
        </ul>
    </li>
    <li>Namespace: arena\security
        <ul>
            <li>function getSecurityObjects(Arena $arena) - lists security objects</li>
            <li>function getSecurityTemplates(Arena $arena, string $type) - lists security templates</li>
        </ul>
    </li>
    <li>Namespace: arena\group
        <ul>
            <li>function getDetails(Arena $arena, int $groupID) - gets details about a group</li>
            <li>function getGroups(Arena $arena, int $catagoryID, array $criteria = []) - lists all the groups that match optional criteria, see below</li>
            <li>function getMembers(Arena $arena, int $groupID, int $roleID = -1, array $fields = []) - lists all the members in a group</li>
            <li>function getMemberDetails(Arena $arena, int $groupID, int $personID, array $fields = []) - gets details about a specific group member</li>
            <li>function getCatagories(Arena $arena) - gets group catagories</li>
            <li>function getCategorieLeaders(Arena $arena, int $categoryID, array $fields = []) - gets leaders within a given catagory</li>
        </ul>
    </li>
    <li>Namespace: arena\promotion
        <ul>
            <li>function getPromotions(Arena $arena, int $topicAreaList, string $areaFilter, array $criteria = []) - gets all current web promotions</li>
        </ul>
    </li>
    <li>Namespace: arena\profile
        <ul>
            <li>function getDetails(Arena $arena, int $profileID) - gets details about a profile</li>
            <li>function getProfiles(Arena $arena, int $profileType) - lists profiles of a given type</li>
            <li>function getParentProfileList(Arena $arena, int $profileID) - lists profiles inside a given parent</li>
            <li>function getMembers(Arena $arena, int $profileID, array $fields = []) - lists members of a given profile</li>
            <li>function getMemberDetails(Arena $arena, int $profileID, int $personID, array $fields = []) - gets details about a member of a given profile</li>
        </ul>
    </li>
</ul>

<h2>Fields & paramaters</h2>
<h3>Person Fields</h3>
<p>ActiveMeter, Addresses (AddressID, AddressTypeID, AddressTypeValue, City, Country, Latitude, Longitude, State, PostalCode, Primary, Proximity, StreetLine1, StreetLine2), Age, AnniversaryDate, AreaID, AreaName, AttributesLink, BirthDate, BlobID, BlobLink, CampusID, CampusName, ContributeIndividually, DateCreated, DateModified, DisplayNotesCount, EmailStatement, Emails (Address), EnvelopeNumber, FamilyID, FamilyLink, FamilyMemberRoleID, FamilyMemberRoleValue, FamilyMembersCount, FamilyID, FamilyName, FirstName, ForeignKey, ForeignKey2, FullName, Gender, GivingUnitID, GraduationDate, Grade, InactiveReasonID, InactiveReasonValue, IncludeOnEnvelope, LastName, MaritalStatusID, MaritalStatusValue, MedicalInformation, MemberStatusID, MemberStatusValue, MiddleName, NickName, Notes, NotesLink, OrganizationID, PersonGUID, PersonID, PersonLink, Phones (Extension, Number, PhoneTypeID, PhoneTypeValue, SMSEnabled, Unlisted), PrintStatement, RecordStatusID, RecordStatusValue, RegionName, SuffixID, SuffixValue, TitleID, TitleValue</p>

<h3>Person paramaters</h3>
<p>Address, altID, areaId, birthdate, email, giftID, loginID, firstName, lastName, personID, phone, profileID, onlyConnected, searchDistance, latitude, longitude, campusID, includeInactive, memberStatus, attributeID, attributeIntValue, attributeVarcharValue,. attributeDateTimeValue, attributeDecimalValue, name</p>

<h3>Group paramaters</h3>
<p>XAxis, YAxis, ZAxis, GroupTopic, UseGroupType, GroupType, MeetingDay1, MeetingDay2, AgeGroup, MaritalPreference, Distance, AreaId, SearchText, LimitSearchResultsClusterID, activeOnly, spotsAvailable</p>

<h3>Promotions paramaters</h3>
<p>topicAreasList, areaFilter, campusId, maxItems, eventsOnly, documentTypeId</p>
