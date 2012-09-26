<?php
class IndexController extends Zend_Controller_Action {
	public function init(){
	
	}
	
	public function indexAction() {
		$auth = new Zend_Session_Namespace();
	}
	
	public function loginAction(){
		$auth = new Zend_Session_Namespace();
		$Users = new Model_User();
		if($auth->activeUser){
			$auth->userData = $Users->fetchRow('UserId='.$auth->activeUser);
			$this->_redirect('/search/flights');
		}
		if($this->_request->isPost()){
			$params = $this->_request->getPost();
			$validator = new Zend_Validate_EmailAddress();
			if(!$validator->isValid($params['email'])){
				foreach($params as $k=>$v){
					$this->view->$k = $v;
				}
				$this->view->message = 'Invalid email address !!';
			} else {
				if ($validator->isValid($params['email']) && $params['password'] != '') {
					$select = $Users->select();
					$select->where('email=?',$params['email']);
					//->where('password=?',md5($params['password']));
					$row = $Users->fetchRow($select);
					if($row){
						$auth->activeUser = $row->UserId;
						$auth->userData = $row;
						$this->_redirect('/search/flights');
					}
				} else {
					foreach($params as $k=>$v){
						$this->view->$k = $v;
					}
					$this->view->message = 'Invlid email address or password!!';
				}
			}
		}
	}
	function logoutAction(){
		$auth = new Zend_Session_Namespace();
		if($auth->activeUser){
			$cache = Zend_Registry::get('cache');
			$cache->clean(Zend_Cache::CLEANING_MODE_ALL);
			Zend_Session::destroy();
		}
		$this->_redirect('/#login');
	}
	
	function fbloginAction(){
		$auth = new Zend_Session_Namespace();
		$this->_helper->layout->disableLayout();
		$this->getHelper('viewRenderer')->setNoRender(true);
		$params = $this->_request->getParams();
		//file_put_contents( $_SERVER['DOCUMENT_ROOT'].'/airto/public/images/users/profile.txt',Zend_Json::encode($params));
		if($params['id'] > 0){
			$Users = new Model_User();
			$select = $Users->select();
			$select->where('facebookId=?',$params['id']);
			$select->where('email=?',$params['email']);
			$row = $Users->fetchRow($select);
			if(!$row){
				$data = array(
					      'firstName' => $params['first_name'],
					      'lastName' => $params['last_name'],
					      'facebookId'=>$params['id'],
					      'email' => $params['email'],
					);
				if(isset($params['hometown'])){
					$data['hometown'] = $params['hometown']['name'];	
				}
				if(isset($params['location'])){
					$data['location'] = $params['location']['name'];
				}
				if(isset($params['education'])){
					$data['educationData'] = Zend_Json::encode($params['education']);
				}
				if(isset($params['work'])){
					$data['companyData'] = Zend_Json::encode($params['work']);
				}
				if(isset($params['gender'])){
					$row->gender = $params['gender'];
				}
				if(isset($params['link'])){
					$row->fbLink = $params['link'];
				}
				$Users->insert($data);
				$row = $Users->fetchRow($select);
			} else {
				if(isset($params['hometown'])){
					$row->hometown = $params['hometown']['name'];	
				}
				if(isset($params['location'])){
					$row->location = $params['location']['name'];
				}
				if(isset($params['education'])){
					$row->educationData = Zend_Json::encode($params['education']);
				}
				if(isset($params['work'])){
					$row->companyData = Zend_Json::encode($params['work']);
				}
				if(isset($params['gender'])){
					$row->gender = $params['gender'];
				}
				if(isset($params['link'])){
					$row->fbLink = $params['link'];
				}
				$row->save();
			}
			//echo '<pre>';print_r( $row);die;
			$auth->activeUser = $row->UserId;
			$auth->userData = $row;
			if(isset($params['pic_square']) && ($params['pic_square'] != $row->pic)){
				$imgInfo = pathinfo($params['pic_square']);
				$pic_square = file_get_contents($params['pic_square']);
				$userPath = $_SERVER['DOCUMENT_ROOT'].$this->view->baseUrl().'/public/images/users';
				if(!is_dir($userPath)){
					mkdir($userPath,0777);	
				}
				file_put_contents($userPath.'/'.$imgInfo['basename'],$pic_square);
				$row->pic = $imgInfo['basename'];
				$row->save();
			}
			//echo '<pre>';print_r($row);die;
			$auth->activeUser = $row->UserId;
			$auth->userData = $row;
			echo Zend_Json::encode(array('msg'=>'success','userId'=>$auth->activeUser));
		} else {
			echo Zend_Json::encode(array('msg'=>'error'));
		}
	}
	function flightsAction(){
		$auth = new Zend_Session_Namespace();
		//echo $auth->activeUser;die;
		if($auth->activeUser){
			$MyWsdl = new My_Wsdl();
			$TARGETBRANCH = 'P107488';
			$MyWsdl->setTargetBranch($TARGETBRANCH);
			$MyWsdl->setUsername('Universal API/uAPI3328713833-136749ba');
			$MyWsdl->setPassword('g2pAqnQrqEYMrwJWgQXtmMKwa');
			$MyWsdl->setUrl('https://emea.copy-webservices.travelport.com/B2BGateway/connect/uAPI/AirService');
			
			//Params
			$Source = 'LHR';
			$Dest = 'JFK';
			$DepartTime="2012-10-08";
			
			$req = <<<EOM
        <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:air="http://www.travelport.com/schema/air_v16_0" xmlns:com="http://www.travelport.com/schema/common_v13_0" >
            <soapenv:Header/>
            <soapenv:Body>
            <air:AvailabilitySearchReq xmlns:air="http://www.travelport.com/schema/air_v16_0" xmlns:com="http://www.travelport.com/schema/common_v13_0" AuthorizedBy="Test" TargetBranch="$TARGETBRANCH">
            <air:SearchAirLeg>
            <air:SearchOrigin>
            <com:Airport Code="$Source" />
            </air:SearchOrigin>
            <air:SearchDestination>
            <com:Airport Code="$Dest" />
            </air:SearchDestination>
            <air:SearchDepTime PreferredTime="$DepartTime" />
            </air:SearchAirLeg>
            </air:AvailabilitySearchReq>
            </soapenv:Body>
            </soapenv:Envelope>
EOM;
			$MyWsdl->setRequest($req);
			//$MyWsdl->sendRequest();
			//$avails = $MyWsdl->getAvailabilitySearchService();
		} else {
			$this->_redirect('/#login');
		}
	}
	function seatsinflightAction(){
		$this->_helper->layout->disableLayout();	
		$auth = new Zend_Session_Namespace();
		if($auth->activeUser){
			$MyWsdl = new My_Wsdl();
			$TARGETBRANCH = 'P7004244';
			$MyWsdl->setTargetBranch($TARGETBRANCH);
			$MyWsdl->setUsername('Universal API/uAPI2203635542-f6f4d5d8');
			$MyWsdl->setPassword('kH=4&e2F8K');
			$MyWsdl->setUrl('https://americas.copy-webservices.travelport.com/B2BGateway/connect/uAPI/AirService');
			//$Carrier = 'EK';
			//$Source = 'SYD';
			//$Dest = 'DXB';
			//$DepartTime="2012-09-18T06:00";
			$params = $this->_request->getParams();
			if(isset($params['flightNumber'])) {
				//Params
				$params = $this->_request->getParams();
				$Carrier = (isset($params['carrier'])?$params['carrier']:'DL');//'DL';
				$this->view->flightNumber = $FlightNumber = $params['flightNumber'];//'1865';
				preg_match('/\((.*?)\)/i',$params['Source'],$origin);
				$Source = isset($origin[1])?$origin[1]:$params['Source'];//'JFK';
				preg_match('/\((.*?)\)/i',$params['Dest'],$dest);
				$Dest = isset($dest[1])?$dest[1]:$params['Dest'];//'SFO';
				$DepartTime = $params['DepartureTime'];//"2012-09-20T07:00";
				$ArrivalTime = $params['ArrivalTime'];//"2012-09-20T10:22";
				$cabinClass = $params['class'];
				$ProviderCode = (isset($params['ProviderCode'])?$params['ProviderCode']:'1G');
				$Key = (isset($params['Key'])?$params['Key']:'0');
				$Group = (isset($params['Group'])?$params['Group']:'0');
			$cache = Zend_Registry::get('cache');
			$hash = 'search_'.md5($DepartTime.$ArrivalTime.$Source.$Dest.$FlightNumber.$cabinClass);
			//echo $hash;die;
			$this->view->departure = $DepartTime;
			if($DepartTime && $FlightNumber){
				$uniqueFlight = md5($FlightNumber.$DepartTime);
				$UserPrivacySettings = new Model_UserPrivacySettings();
				if($rows = $UserPrivacySettings->getFlightPeople($uniqueFlight)){
					$temp = array();
					foreach($rows as $row){
						$seatOccupier = '<div class="userTip">
							<table width="100%">
							  <tr>
								<td>
									<img src="'.$this->view->baseUrl().'/public/images/users/'.(isset($row->pic)?$row->pic:'no_pic.jpg').'"></td>
								<td valign="top">'.ucwords($row->firstName.' '.$row->lastName).'</td>
							  </tr>
							  <tr>
								<td> Seat '.$row->seatNumber.'</td>
								<td>&nbsp;</td>
							  </tr>
							</table>
						    </div>';
						$temp["{$row->seatNumber}"] = $seatOccupier;    
					}
					$this->view->seatOccupier = $temp;
					//unset($temp);
				}
			}
			if ( ($data = $cache->load($hash)) === false ) {
			$req = <<<EOM
	<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
	  <soap:Body>
	    <ns2:SeatMapReq AuthorizedBy="TEST" TargetBranch="$TARGETBRANCH" xmlns="http://www.travelport.com/schema/common_v14_0" xmlns:ns2="http://www.travelport.com/schema/air_v17_0" xmlns:ns3="http://www.travelport.com/schema/rail_v11_0" xmlns:ns4="http://www.travelport.com/schema/vehicle_v16_0" xmlns:ns5="http://www.travelport.com/schema/passive_v13_0" xmlns:ns6="http://www.travelport.com/schema/hotel_v16_0" xmlns:ns7="http://www.travelport.com/schema/universal_v15_0">
	      <BillingPointOfSaleInfo OriginApplication="UAPI"/>
	      <ns2:AirSegment Carrier="$Carrier" DepartureTime="$DepartTime" ArrivalTime="$ArrivalTime" Destination="$Dest" FlightNumber="$FlightNumber" Group="$Group" Key="$Key" Origin="$Source" ProviderCode="$ProviderCode"/>
	      <ns2:BookingCode Code="$cabinClass"/>
	    </ns2:SeatMapReq>
	  </soap:Body>
	</soap:Envelope>
EOM;
//echo $req;die;
				
				$MyWsdl->setRequest($req);
				$MyWsdl->sendRequest();
				$this->view->seats = $MyWsdl->getSeatMap();
			$cache->save($this->view->seats,$hash);
			} else {
				$this->view->seats = $data;	
			}
			}
		}
	}
	
	function pnrdetailsAction(){
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
		$auth = new Zend_Session_Namespace();
		if($auth->activeUser){
			$MyWsdl = new My_Wsdl();
			$TARGETBRANCH = 'P7004244';
			$MyWsdl->setTargetBranch($TARGETBRANCH);
			$MyWsdl->setUsername('Universal API/uAPI2203635542-f6f4d5d8');
			$MyWsdl->setPassword('kH=4&e2F8K');
			if($this->_request->isPost()){
			$MyWsdl->setUrl("https://americas.copy-webservices.travelport.com/B2BGateway/connect/uAPI/UniversalRecordService");
			
			$params = $this->_request->getPost();
			$pnr = 'ZLDF7K';//'PZZ9C2';//$params['pnrNumber'];//'ZLDF7K';
			$req = <<<EOM
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
<univ:UniversalRecordRetrieveReq AuthorizedBy="TEST" TraceId="TCB2012" TargetBranch="$TARGETBRANCH" UniversalRecordLocatorCode="$pnr" xmlns:univ="http://www.travelport.com/schema/universal_v17_0" xmlns:com="http://www.travelport.com/schema/common_v16_0">
  <com:BillingPointOfSaleInfo OriginApplication="UAPI" />
</univ:UniversalRecordRetrieveReq>
</soap:Body>
</soap:Envelope>
EOM;
				$MyWsdl->setRequest($req);
				$MyWsdl->sendRequest();
				$pnrdetails = $MyWsdl->getPNRDetails();
	//echo '<pre>';print_r($pnrdetails);die;
		$params = array(
			'flightNumber'=> $pnrdetails['AirSegment']['Value']['FlightNumber'],
			'carrier'=>$pnrdetails['AirSegment']['Value']['Carrier'],
			'Source'=>$pnrdetails['AirSegment']['Value']['Origin'],
			'Dest'=>$pnrdetails['AirSegment']['Value']['Destination'],
			'DepartureTime'=>$pnrdetails['AirSegment']['Value']['DepartureTime'],
			'ArrivalTime'=>$pnrdetails['AirSegment']['Value']['ArrivalTime'],
			'class'=>$pnrdetails['AirSegment']['Value']['ClassOfService'],
			'Key'=>$pnrdetails['AirSegment']['Value']['Key'],
			'Group'=>$pnrdetails['AirSegment']['Value']['Group'],
			'ProviderCode'=>$pnrdetails['AirSegment']['Value']['ProviderCode']
		);
		$this->_helper->actionStack('seatsinflight','index','',$params);
			}
		}
		//die;
	}
	
	function bookseatAction(){
		
	}
	
	function usersettingsAction(){
		$this->_helper->layout->disablelayout();
		$auth = new Zend_Session_Namespace();
		if($auth->activeUser){
			$this->view->flightNumber = $flightNumber = $this->_request->getParam('flight');
			$this->view->seatNumber = $seatNumber = $this->_request->getParam('seat');
			$this->view->departure = $this->_request->getParam('departure');
			if($this->_request->isPost()){
				$params = $this->_request->getPost();
				$UserPrivacySettings = new Model_UserPrivacySettings();
				$privacy = $params['privacy'];
				$bookedId = 0;
				if(isset($params['privacy'])){
					$uniqueFlight = md5($params['flightNumber'].$params['departure']);
					$data = array('fkUserId'=> $auth->activeUser,
						      'flightNumber' => $params['flightNumber'],
						      'seatNumber' => $params['seatNumber'],
						      'departureDate' => $params['departure'],
						      'uniqueFlight' => $uniqueFlight,
						      'privacyData' => Zend_Json::encode($params['privacy'])
						      );
					if($row = $UserPrivacySettings->fetchRow('fkUserId='.$auth->activeUser.' AND (uniqueFlight is Not Null And uniqueFlight=\''.$uniqueFlight.'\')')){
						$bookedId = $row->id;
						$UserPrivacySettings->update($data,'id='.$bookedId);
					} else {
						$bookedId = $UserPrivacySettings->insert($data);	
					}
						
				}
			}
		}
	}
	
	function peopleonflightAction(){
		$this->_helper->layout->disableLayout();
		$auth = new Zend_Session_Namespace();
		if($auth->activeUser){
			if($this->_request->isPost()){
				$params = $this->_request->getPost();	
				$flight = $params['flight'];
				$departure = $params['departure'];
				$uniqueFlight = md5($flight.$departure);
				$UserPrivacySettings = new Model_UserPrivacySettings();
				if($rows = $UserPrivacySettings->getFlightPeople($uniqueFlight)){
					$this->view->records = $rows;
				}		
			}
		}
	}
	function flightdetailsAction(){
		$auth = new Zend_Session_Namespace();
		if($auth->activeUser){
			$MyWsdl = new My_Wsdl();
			$TARGETBRANCH = 'P7004244';
			$MyWsdl->setTargetBranch($TARGETBRANCH);
			$MyWsdl->setUsername('Universal API/uAPI2203635542-f6f4d5d8');
			$MyWsdl->setPassword('kH=4&e2F8K');
			$MyWsdl->setUrl("https://americas.copy-webservices.travelport.com/B2BGateway/connect/uAPI/FlightService");
			
			if($this->_request->isPost()){
			$params = $this->_request->getPost();	
			$req = <<<EOM
	<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
		<soap:Body>
		<FlightDetailsReq AuthorizedBy="TEST" TargetBranch="$TARGETBRANCH" xmlns="http://www.travelport.com/schema/air_v20_0" xmlns:com="http://www.travelport.com/schema/common_v17_0">
			<com:BillingPointOfSaleInfo OriginApplication="UAPI" />
			<AirSegment Key="1T" Group="0" Carrier="BA" FlightNumber="2936" Origin="LGW" Destination="EDI" DepartureTime="2012-12-03T08:25:00.000+00:00" ArrivalTime="2012-12-03T09:50:00.000+00:00">
			</AirSegment>
		</FlightDetailsReq>
		</soap:Body>
	</soap:Envelope>
EOM;
				$MyWsdl->setRequest($req);
				$MyWsdl->sendRequest();
				$this->view->details = $MyWsdl->getFlightDetails();
			print_r($this->view->details);
			}
		}
		die;
	}
}