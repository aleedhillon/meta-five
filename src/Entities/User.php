<?php


namespace AleeDhillon\MetaFive\Entities;


/**
 * Class User
 * @package AleeDhillon\MetaFive\Entities
 */
class User
{
    protected $login;
    protected $name;
    protected $email;
    protected $main_password;
    protected $group;
    protected $leverage;
    protected $zip_code;
    protected $country;
    protected $state;
    protected $city;
    protected $address;
    protected $phone;
    protected $phone_password;
    protected $investor_password;
    protected $agent;
    protected $company;

    /**
     * @return mixed
     */
    public function getLogin()
    {
        return $this->login;
    }

    public function setLogin($login)
    {
        $this->login = $login;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMainPassword()
    {
        return $this->main_password;
    }

    public function setMainPassword($main_password)
    {
        $this->main_password = $main_password;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getGroup()
    {
        return $this->group;
    }

    public function setGroup($group)
    {
        $this->group = $group;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLeverage()
    {
        return $this->leverage;
    }


    public function setLeverage($leverage)
    {
        $this->leverage = $leverage;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getZipCode()
    {
        return $this->zip_code;
    }


    public function setZipCode($zip_code)
    {
        $this->zip_code = $zip_code;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCountry()
    {
        return $this->country;
    }


    public function setCountry($country)
    {
        $this->country = $country;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getState()
    {
        return $this->state;
    }

    public function setState($state)
    {
        $this->state = $state;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }


    public function setCity($city)
    {
        $this->city = $city;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }

    public function setAddress($address)
    {
        $this->address = $address;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    public function setPhone($phone)
    {
        $this->phone = $phone;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPhonePassword()
    {
        return $this->phone_password;
    }


    public function setPhonePassword($phone_password)
    {
        $this->phone_password = $phone_password;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getInvestorPassword()
    {
        return $this->investor_password;
    }

    public function setInvestorPassword($investor_password)
    {
        $this->investor_password = $investor_password;
        return $this;
    }

    public function setAgent($agentLogin)
    {
        $this->agent = $agentLogin;
        return $this;
    }

    public function getAgent()
    {
        return $this->agent;
    }

    public function setCompany($company)
    {
        $this->company = $company;

        return $this;
    }

    public function getCompany()
    {
        return $this->company;
    }
}
