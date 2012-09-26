<?php
class SearchController extends Zend_Controller_Action {
	public function init(){
	
	}
	
	public function indexAction() {
		$auth = new Zend_Session_Namespace();
            redirect('/search/flights');
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
			$this->view->arrival = $ArrivalTime;
			$this->view->departure = $DepartTime;
			$this->view->destination = $Dest;
			if($DepartTime && $FlightNumber){
				$uniqueFlight = md5($FlightNumber.$DepartTime);
				$UserPrivacySettings = new Model_UserPrivacySettings();
				if($rows = $UserPrivacySettings->getFlightPeople($uniqueFlight)){
					$temp = array();
					foreach($rows as $row){
						$seatOccupier = '<div class="userTip">
								<div class="passengerDetial">
									<div class="passengerImg">
									<img src="'.$this->view->baseUrl().'/public/images/users/'.(empty($row->pic)?'no_pic.jpg':$row->pic).'" width="50" height="50" alt="" />
									<span>Seat '.str_replace('-','',$row->seatNumber).'</span>
									<a href="'.($row->fbLink?$row->fbLink:'#').'"><img src="'.$this->view->baseUrl().'/public/images/S-facebook.png" alt="facebook" /></a>
									<a href="#"><img src="'.$this->view->baseUrl().'/public/images/s-in.png" alt="Linked In" /></a>
									<a class="last" href="#"><img src="'.$this->view->baseUrl().'/public/images/s-twitter.png" alt="Twitter" /></a>
								    </div>
								    <div class="passengerRight">
									<h5>'.ucwords($row->firstName.' '.$row->lastName).'</h5>
									<sub>Cmo, at&t </sub>
									<span>'.($row->location?$row->location:'Los Angeles, CA, USA').'</span>
									<p>You and sandy have the same <a href="#">friend</a>, <a href="#">music preferences</a> and <a href="#">favorite author</a> </p>
								    </div>
								</div>
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
			$cache = Zend_Registry::get('cache');
			$hash = 'pnrsearch_'.md5($pnr);
			if ( ($pnrdetails = $cache->load($hash)) === false ) {
				$MyWsdl->sendRequest();
				$pnrdetails = $MyWsdl->getPNRDetails();
				$cache->save($pnrdetails,$hash);
			}
		$params = array(
			'flightNumber'=> $pnrdetails['AirSegment']['Value']['FlightNumber'],
			'carrier'=>$pnrdetails['AirSegment']['Value']['Carrier'],
			'Source'=>$pnrdetails['AirSegment']['Value']['Origin'],
			'Dest'=>$pnrdetails['AirSegment']['Value']['Destination'],
			//'DepartureTime'=>$pnrdetails['AirSegment']['Value']['DepartureTime'],
			//'ArrivalTime'=>$pnrdetails['AirSegment']['Value']['ArrivalTime'],
			'DepartureTime'=> date('Y-m-d',strtotime("+1 day")).'T07:00:00.000-04:00',
			'ArrivalTime'=>date('Y-m-d',strtotime("+1 day")).'T10:25:00.000-07:00',
			'class'=>$pnrdetails['AirSegment']['Value']['ClassOfService'],
			'Key'=>$pnrdetails['AirSegment']['Value']['Key'],
			'Group'=>$pnrdetails['AirSegment']['Value']['Group'],
			'ProviderCode'=>$pnrdetails['AirSegment']['Value']['ProviderCode']
		);
		$this->_helper->actionStack('seatsinflight','search','',$params);
			}
		}
		//die;
	}
	
	function hotelmapAction(){
		$this->_helper->layout->disablelayout();
		if($this->_request->getParam('destCode')){
			$post = $this->_request->getParams();
			$destCode = $post['destCode'];
			$arrivalTime = $post['flightArrival'];
			$departureTime = $post['flightDeparture'];
			$AirportCodes = new Model_AirportCodes();
			$row = $AirportCodes->getLatLang($destCode);
			if($row){
				$d1 = explode('T',$departureTime);
				$checkInDate = date('d/m/Y',strtotime($d1[0]));
				
				$d2 = explode('T',$arrivalTime);
				$checkOutDate = date('d/m/Y',strtotime($d2[0]));
				
				//$this->view->centerLat = $row->latitude;
				//$this->view->centerLong = $row->longitude;
				$this->view->centerLat = 37.6190;
				$this->view->centerLong = -122.3749;
				//$request = 'http://hotelsearchengine.amadeus.com/thackgeosearch?fromRef=Airto&centerLat='.$this->view->centerLat.'&centerLon='.$this->view->centerLong.'&radius=150000&checkInDate='.$checkInDate.'&checkOutDate='.$checkOutDate;
				$request = 'http://hotelsearchengine.amadeus.com/thackgeosearch?fromRef=Airto&centerLat='.$this->view->centerLat.'&centerLon='.$this->view->centerLong.'&radius=50000&checkInDate=17/09/2012&checkOutDate=18/09/2012';
  
				//echo $request;die;
				$this->view->response = file_get_contents($request);
			}
			
			
		}
	}
	function taxisAction(){
		$this->_helper->layout->disablelayout();
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