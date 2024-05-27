<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Nxtech\Customform\Model;

use Magento\Framework\Model\AbstractModel;

class Customform extends AbstractModel 
{

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\Nxtech\Customform\Model\ResourceModel\Customform::class);
    }
}

