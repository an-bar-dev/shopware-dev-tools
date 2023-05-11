<?php declare(strict_types=1);

namespace AnBarDev\ShopwareDevTools\Trait;

trait FileTemplatesTrait {

  use ComposerFileTemplateTrait;
  use EntityFileTemplateTrait;
  use EntityDefinitionFileTemplateTrait;
  use EntityCollectionFileTemplateTrait;
  use EntityRepositoryFileTemplateTrait;
  use EntityHydratorFileTemplateTrait;
}