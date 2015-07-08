<?php

namespace MBHS\Bundle\BaseBundle\Model;

interface UserInterface extends \FOS\UserBundle\Model\UserInterface
{
    const GENDER_FEMALE  = 'f';
    const GENDER_MALE    = 'm';
    const GENDER_UNKNOWN = 'u';

    /**
     * @return string
     */
    public function getTwoStepVerificationCode();

    /**
     * @param string $code
     *
     * @return string
     */
    public function setTwoStepVerificationCode($code);
}