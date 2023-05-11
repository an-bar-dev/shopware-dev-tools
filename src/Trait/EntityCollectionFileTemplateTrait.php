<?php declare(strict_types=1);

namespace AnBarDev\ShopwareDevTools\Trait;

trait EntityCollectionFileTemplateTrait
{

    private string $entityCollectionTemplate = '<?php declare(strict_types=1);

namespace #namespace#;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void               add(#entityName#Entity $entity)
 * @method void               set(string $key, #entityName#Entity $entity)
 * @method #entityName#Entity[]    getIterator()
 * @method #entityName#Entity[]    getElements()
 * @method #entityName#Entity|null get(string $key)
 * @method #entityName#Entity|null first()
 * @method #entityName#Entity|null last()
 */
class #entityName#Collection extends EntityCollection
{
    public function getApiAlias(): string
    {
        return \'#entity_name#\';
    }

    protected function getExpectedClass(): string
    {
        return #entityName#Entity::class;
    }
}';
}