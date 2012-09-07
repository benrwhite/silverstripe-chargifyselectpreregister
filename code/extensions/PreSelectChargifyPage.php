<?php

class PreSelectChargifyPage extends ChargifySubscriptionPage {
	static $has_one = array (
		'PreRegisterRedirectLocation' => 'MemberProfilePage'
	);

	public function getCMSFields() {
		$fields = parent::getCMSFields();

		$fields->addFieldToTab(
			'Root.Behaviour',
			new TreeDropdownField('PreRegisterRedirectLocationID',
				_t('MemberProfiles.REDIRECT_TARGET', 'Redirect to page'), 'MemberProfilePage')
		);

		return $fields;
	}
}

class PreSelectChargifyPage_Controller extends ChargifySubscriptionPage_Controller {
	public static $allowed_actions = array(
		'afterregistration',
		'beforeregistration',
	);

	public function init() {
		$return = Page_Controller::init();

		if (!Member::currentUserID()){
			if(!$this->data()->PreRegisterRedirectLocationID) {
				return Security::permissionFailure($this, array(
					'default' => 'You must be logged in to manage your subscription.'
				));
			}
		}
	}

	public function afterregistration($request){
		$product = Session::get("PreSelectChargifyPage.{$this->ID}.product");
		Session::clear("PreSelectChargifyPage.{$this->ID}.product");
		return $this->redirect($this->productLink($product));
	}

	public function beforeregistration($request){
		if (!SecurityToken::inst()->checkRequest($request)) {
			return $this->httpError(400);
		}

		$product      = $request->param('ID');
		Session::set("PreSelectChargifyPage.{$this->ID}", array(
			'product' => $product,
		));
		Session::set('MemberProfile.REDIRECT', $this->Link('afterregistration'));
		Session::save();

		return $this->redirect($this->data()->PreRegisterRedirectLocation()->Link());
	}

	/**
	 * @return DataObjectSet
	 */
	public function Products() {
		$products = $this->data()->Products();
		$memberid = Member::currentUserID();
		$service  = ChargifyService::instance();
		$conn     = $service->getConnector();
		$sub      = $this->getChargifySubscription();
		$result   = new DataObjectSet();

		if (!count($products)) return;

		foreach ($products as $link) {
			if (!$product = $conn->getProductByID($link->ProductID)) {
				continue;
			}

			$data = $service->getCastedProductDetails($product);

			if ($this->HasActiveSubscription()) {
				if ($sub->product->id == $product->id) {
					$data->setField('Active', true);
				} else {
					$link = Controller::join_links(
						$this->Link(), 'upgrade', $product->id
					);
					$link = SecurityToken::inst()->addToUrl($link);

					$data->setField('ActionTitle', 'Change subscription');
					$data->setField('ActionLink', $link);
					$data->setField('ActionConfirm', true);
				}
			} else {
				if ($sub) {
					if ($sub->product->id == $product->id) {
						$link = SecurityToken::inst()->addToUrl($this->Link('reactivate'));

						$data->setField('ActionTitle', 'Re-activate');
						$data->setField('ActionLink', $link);
						$data->setField('ActionConfirm', true);
					}
				} else {
					if($memberid){
						$link = $this->productLink($product->id);
					}
					else {
						$link = Controller::join_links(
							$this->Link(), 'beforeregistration', $product->id
						);						
						$link = SecurityToken::inst()->addToUrl($link);
					}
					$data->setField('ActionTitle', 'Subscribe');
					$data->setField('ActionLink', $link);
				}
			}

			$result->push($data);
		}

		return $result;
	}

	private function productLink($id) {
		$member   = Member::currentUser();
		$service  = ChargifyService::instance();
		$reference = implode('-', array(
			$member->ID, $this->ID, $service->generateToken($member->ID, $this->ID)
		));

		return Controller::join_links(
			ChargifyConfig::get_url(),
			'h', $id,
			'subscriptions/new',
			'?first_name=' . urlencode($member->FirstName),
			'?last_name='  . urlencode($member->Surname),
			'?email='      . urlencode($member->Email),
			'?reference='  . urlencode($reference)
		);
	}
}
