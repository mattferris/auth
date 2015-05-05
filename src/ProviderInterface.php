<?php

namespace MattFerris\Auth;

interface ProviderInterface
{
    /**
     * @return array
     */
    public function provides();
} 
