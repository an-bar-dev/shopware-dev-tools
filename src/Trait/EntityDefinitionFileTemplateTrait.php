<?php declare(strict_types=1);

namespace AnBarDev\ShopwareDevTools\Trait;

trait EntityDefinitionFileTemplateTrait
{
    private string $entityDefinitionXmlTemplate = <<<EOL

<service id="#serviceId#">
    <tag name="shopware.entity.definition" entity="#entity_name#" />
</service>

EOL;


    private string $entityDefinitionTemplate = <<<EOL
<?php declare(strict_types=1);

namespace #namespace#;

use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\CreatedAtField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\UpdatedAtField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class #entityName#Definition extends EntityDefinition
{
    public const ENTITY_NAME = '#entity_name#';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return #entityName#Collection::class;
    }

    public function getEntityClass(): string
    {
        return #entityName#Entity::class;
    }

    public function getHydratorClass(): string
    {
        return #entityName#Hydrator::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            new CreatedAtField(),
            new UpdatedAtField()
        ]);
    }
}
EOL;
}
