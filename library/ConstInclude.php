<?php
define('SITE_URL', 'http://'.$_SERVER['HTTP_HOST']);
if($_SERVER['HTTP_HOST'] == 'seatassignmate.com' || $_SERVER['HTTP_HOST'] == 'www.seatassignmate.com'){
//Airto
//App ID: 	341787525913984
//App Secret: 	ec88f89cec55174e00982d5433a84052    
    /* Facebook */
    define('FB_APP_ID','341787525913984');
    define('FB_APP_SECRET','ec88f89cec55174e00982d5433a84052');
    /* Linkedin
    
    
    Company:

    Airto
    Application Name:

    airto
    API Key:

    h4up0lwo8r3x
    Secret Key:

    a93U1HSIhPSmfR68
    OAuth User Token:

    dda2a8f9-fbc7-47ba-9126-a6bc5cb259f1
    OAuth User Secret:

    380b58c7-4fd0-4dea-9335-c0c0b0afcb6f

    */
    define('LK_API_KEY','h4up0lwo8r3x');
    define('LK_API_SECRET','a93U1HSIhPSmfR68');
    define('LK_OAuth_User_Token','dda2a8f9-fbc7-47ba-9126-a6bc5cb259f1');
    define('LK_OAuth_User_Secret','380b58c7-4fd0-4dea-9335-c0c0b0afcb6f');
    define('GMAP_KEY','AIzaSyAjf47CJmWm7fzMtiHyxoG1F7n4iM_t0S0');
} else {
    /* Facebook */
    define('FB_APP_ID','100874226735560');
    define('FB_APP_SECRET','8abb2b4d0a991abbcb5c6dc225b5cbcc');
    /* Linkedin */
    define('LK_API_KEY','la7pym0bp4mf');
    define('LK_API_SECRET','8bVoMuNsPXZNZh9J');
    define('LK_OAuth_User_Token','848afeb8-2d25-4e7c-840c-83f4a18ac8fa');
    define('LK_OAuth_User_Secret','b2aa6c11-53d7-4bf9-82d2-353d442b0701');
    define('GMAP_KEY','AIzaSyAjf47CJmWm7fzMtiHyxoG1F7n4iM_t0S0');
}

