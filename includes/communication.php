<?php

class Communication extends Namesilo {
	
	var $service, $client, $company;
	
	public function __construct( $service ) {
		Loader::loadModels( $this, array( "Clients", "Companies" ) );
		$this->service = $service;
		$this->client = $this->Clients->get( $service->client_id, false );
		$this->company = $this->Companies->get( $this->client->company_id );
	}
	
	public function getNotices() {
		$notices = Configure::get( 'Namesilo.notices' );
		/*
		$notices = array(
			'renewal_reminder' => Language::_( "Namesilo.notices.renewal_reminder", true ),
			'expiry_notice' => Language::_( "Namesilo.notices.expiry_notice", true ),
			'suspension_notice' => Language::_( "Namesilo.notices.suspension_notice", true ),
		);
		*/
		return $notices;
	}
	
	public function send( array $post ) {
		
		if ( empty ( $notice = $post['notice'] ) || !array_key_exists( $notice, $this->getNotices() ) ) {
			return false;
		}
		
		if ( !isset ( $this->Emails ) ) {
			Loader::loadModels( $this, array( "Emails" ) );
		}
		
		$template = $this->Emails->getByType( $this->company->id, "service_suspension" );
		$from = $template->from;
		$from_name = $template->from_name;
		
		$tags = array (
			'first_name' => $this->client->first_name,
			'domain' => $this->getServiceName( $this->service ),
			'expiry_date' => strftime( '%F', strtotime( $this->service->date_renews ) ),
			'from_name' => $from_name,
			'client_url' => $this->company->hostname,
		);
		
		$result = $this->Emails->sendCustom( $from, $from_name, $this->client->email, Language::_( "Namesilo.notices.{$notice}.subject", true ), array( 'text' => Language::_( "Namesilo.notices.{$notice}.text", true ) ), $tags, null, $from );
		
		return $result;
	}
	
}
