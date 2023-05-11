<?php declare(strict_types=1);

namespace AnBarDev\ShopwareDevTools\Trait;

trait EntityFileTemplateTrait
{
    private string $entityTemplate = <<<EOL
<?php declare(strict_types=1);

namespace #namespace#;

use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class #entityName#Entity extends Entity
{
    use EntityIdTrait;

}
EOL;

}