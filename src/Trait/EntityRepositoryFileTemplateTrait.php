<?php declare(strict_types=1);

namespace AnBarDev\ShopwareDevTools\Trait;

trait EntityRepositoryFileTemplateTrait
{
    private string $entityRepositoryTemplate = '<?php declare(strict_types=1);

namespace #namespace#;

use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use #namespace#\#entityName#Definition;



class #entityName#Repository extends EntityRepository
{
    public function getDefinitionClass(): string
    {
        return #entityName#Definition::class;
    }
}
';
}
