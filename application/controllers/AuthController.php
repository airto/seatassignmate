<?php
class AuthController extends Zend_Controller_Action
{
    public function linkedinAction()
    {
            $config = array(
                'callbackUrl' => 'http://example.com/callback.php',
                'siteUrl' => 'http://twitter.com/oauth',
                'consumerKey' => LK_API_KEY,
                'consumerSecret' => LK_API_SECRET
            );
             
            $statusMessage = 'I\'m posting to Twitter using Zend_Oauth!';
             
            $token = unserialize($_SESSION['TWITTER_ACCESS_TOKEN']);
            $client = $token->getHttpClient($configuration);
            $client->setUri('http://twitter.com/statuses/update.json');
            $client->setMethod(Zend_Http_Client::POST);
            $client->setParameterPost('status', $statusMessage);
            $response = $client->request();
             
            $data = Zend_Json::decode($response->getBody());
            $result = $response->getBody();
            if (isset($data->text)) {
                $result = 'true';
            }
            echo $result;
    }
}
