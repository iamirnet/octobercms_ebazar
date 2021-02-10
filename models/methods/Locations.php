<?php

namespace iAmirNet\Ebazar\Models\Methods;

trait Locations
{
    public function getState($state) {
        $state = array_values(array_filter(
                $this->getStates(),
                function ($e) use (&$state) {
                    return $e->pName == $state;
                }
            )
        );
        $this->state = isset($state[0]) ? $state[0]->Code : 0;
        return $this->state;
    }

    public function getCity($city, $stateId = null) {
        $this->state = $stateId ? : $this->state;
        $city = array_values(array_filter(
                $this->getCities($this->state),
                function ($e) use (&$city) {
                    return $e->pName == $city;
                }
            )
        );
        $this->city = isset($city[0]) ? $city[0]->Code : 0;
        return $this->city;
    }

    public function getStates()
    {
        $state =  $this->_curl([], 'getStats');
        $this->states = isset($state->result) ? $state->result : [];
        return $this->states;
    }

    public function getCities($state = 1)
    {
        $this->state = $state;
        $cities = $this->_curl(['ProvinceCode' => $this->state], 'getCities');
        $this->cities = isset($cities->result) ? $cities->result : [];
        return $this->cities;
    }
}
