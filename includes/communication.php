<?php

class Communication extends Namesilo
{
    private $service;
    private $client;
    private $company;

    public function __construct($service)
    {
        Loader::loadModels($this, ['Clients', 'Companies']);
        $this->service = $service;
        $this->client = $this->Clients->get($service->client_id, false);
        $this->company = $this->Companies->get($this->client->company_id);
    }

    public function getNotices()
    {
        return Configure::get('Namesilo.notices');
    }

    public function send(array $post)
    {
        $notice = isset($post['notice']) ? $post['notice'] : '';
        if (empty($notice) || !array_key_exists($notice, $this->getNotices())) {
            return false;
        }

        if (!isset($this->Emails)) {
            Loader::loadModels($this, ['Emails']);
        }

        $template = $this->Emails->getByType($this->company->id, 'service_suspension');
        $from = $template->from;
        $from_name = $template->from_name;

        $tags = [
            'first_name' => $this->client->first_name,
            'domain' => $this->getServiceName($this->service),
            'expiry_date' => strftime('%F', strtotime($this->service->date_renews)),
            'from_name' => $from_name,
            'client_url' => $this->company->hostname
        ];

        $result = $this->Emails->sendCustom(
            $from,
            $from_name,
            $this->client->email,
            Language::_('Namesilo.notices.' . $notice . '.subject', true),
            ['text' => Language::_('Namesilo.notices.' . $notice . '.text', true)],
            $tags,
            null,
            $from
        );

        return $result;
    }
}
