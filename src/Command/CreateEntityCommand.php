<?php declare(strict_types=1);

namespace AnBarDev\ShopwareDevTools\Command;

use AnBarDev\ShopwareDevTools\Trait\FileTemplatesTrait;
use DOMDocument;
use DOMXPath;
use Shopware\Core\Framework\Adapter\Console\ShopwareStyle;
use Shopware\Core\Framework\Log\Package;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

#[Package('core')]
class CreateEntityCommand extends Command
{

    use FileTemplatesTrait;

    private ?string $name;
    private ?string $entityName;
    private ?string $namespace;
    private ?string $createRepository;
    private ?string $entity_name;
    private ?string $directory;
    private ?string $entityNamespace;
    private ?string $repositoryNamespace;

    protected static $defaultName = 'an-bar-dev:shopware-dev-tools:create-entity';

    private string $servicesXmlTemplate = <<<EOL
<?xml version="1.0" ?>

<container xmlns="http://symfony.com/schema/dic/services"
           xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
           xsi:schemaLocation="http://symfony.com/schema/dic/services http://symfony.com/schema/dic/services/services-1.0.xsd">

    <services>

    </services>
</container>
EOL;


    /**
     * @var string
     */
    private string $projectDir;

    /**
     * @internal
     */
    public function __construct(string $projectDir)
    {
        parent::__construct();
        $this->projectDir = $projectDir;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::OPTIONAL)
            ->addArgument('entityName', InputArgument::OPTIONAL)
            ->addArgument('namespace', InputArgument::OPTIONAL)
            ->addOption('create-config', 'c', InputOption::VALUE_NONE, 'Create config.xml')
            ->setDescription('Creates a plugin skeleton');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->name = $input->getArgument('name');

        if (!$this->name) {
            $question = new Question('Please enter a plugin name: ');
            $this->name = $this->getHelper('question')->ask($input, $output, $question);
        }

        $this->name = ucfirst($this->name);
        $this->directory = $this->projectDir . '/custom/plugins/' . $this->name;
        $this->entityName = $input->getArgument('entityName');

        while (!$this->entityName) {
            $question = new Question('Please enter the entity name: ');
            $this->entityName = $this->getHelper('question')->ask($input, $output, $question);
        }

        $this->entityName = ucfirst($this->entityName);

        $this->namespace = $input->getArgument('namespace');
        if (!$this->namespace) {
            $question = new Question('Please enter a namespace: ');
            $this->namespace = $this->getHelper('question')->ask($input, $output, $question);
        }
        $this->namespace = ucfirst($this->namespace);
        $this->entityNamespace = sprintf('%s\\Entity\\%s', $this->namespace, $this->entityName);

        if (!mkdir($concurrentDirectory = $this->directory . '/src/Entity/' . $this->entityName, 0777, true) && !is_dir($concurrentDirectory)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
        }

        $this->createEntity($input, $output);
        $this->createEntityDefinition($input, $output);
        $this->createEntityCollection($input, $output);
        $this->createEntityHydrator($input, $output);
        $this->createRepository($input, $output);

        return self::SUCCESS;
    }

    private function createRepository(InputInterface $input, OutputInterface $output): void
    {
        $io = new ShopwareStyle($input, $output);

        $io->writeln('<info>Success!</info>');
        $io->writeln('<question>Are you sure?</question>');
        $io->writeln('<error>Error!</error>');

        $question = new Question('Do you want to create entity repository? <comment>[Y/n] (default: Y)</comment>');

        $this->createRepository = $this->getHelper('question')->ask($input, $output, $question);

        if ($this->createRepository === '' || strtolower($this->createRepository) === 'y') {
            $createRepository = true;
        } else {
            $createRepository = false;
        }

        if ($createRepository) {
            if (!mkdir($concurrentDirectory = $this->directory . '/src/Repository/', 0777, true) && !is_dir($concurrentDirectory)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
            }


            $entityRepositoryFile = sprintf('%s/src/Repository/%sRepository.php',
                $this->directory,
                $this->entityName,
            );

            $entityRepository = str_replace(
                ['#namespace#', '#entityName#'],
                [$this->namespace . '\Repository', $this->entityName],
                $this->entityRepositoryTemplate
            );

            file_put_contents($entityRepositoryFile, $entityRepository);
        }
    }

    private function createEntityCollection(InputInterface $input, OutputInterface $output): void
    {
        $entityCollectionFile = sprintf('%s/src/Entity/%s/%sCollection.php',
            $this->directory,
            $this->entityName,
            $this->entityName
        );

        $this->entity_name = strtolower($this->entityName);
        $this->entity_name = preg_replace('/[\s_]+/', '-', $this->entity_name);
        $this->entity_name = preg_replace('/[^a-z0-9\-]+/', '_', $this->entity_name);
        $this->entity_name = preg_replace('/\-+/', '_', $this->entity_name);
        $this->entity_name = trim($this->entity_name, '_');

        $entityCollection = str_replace(
            ['#namespace#', '#entityName#', '#entity_name#'],
            [$this->entityNamespace, $this->entityName, $this->entity_name],
            $this->entityCollectionTemplate
        );
        file_put_contents($entityCollectionFile, $entityCollection);
    }

    private function createEntityDefinition(InputInterface $input, OutputInterface $output): void
    {
        $entityDefinitionFile = sprintf('%s/src/Entity/%s/%sDefinition.php',
            $this->directory,
            $this->entityName,
            $this->entityName
        );

        $entityDefinition = str_replace(
            ['#namespace#', '#entityName#'],
            [$this->entityNamespace, $this->entityName],
            $this->entityDefinitionTemplate
        );
        file_put_contents($entityDefinitionFile, $entityDefinition);

        $serviceXmlFile = sprintf('%s/src/Resources/config/services.xml', $this->directory);

        // Load the XML file into a DOMDocument object
        $xml = new DOMDocument();
        $xml->load('path/to/your/file.xml');

        // Find the services element using XPath
        $xpath = new DOMXPath($xml);
        $services = $xpath->query('//services')->item(0);

        $entityDefinitionXml = str_replace(
            ['#serviceId#', '#entity_name#'],
            [sprintf('%s/%sDefinition', $this->entityNamespace, $this->entityName), $this->entity_name],
            $this->entityDefinitionXmlTemplate
        );
        try {
            // Create a new DOMElement for your string
            $newString = $xml->createElement('string', $entityDefinitionXml);
            // Insert the new element before the closing </services> tag
            $services?->insertBefore($newString, $services?->lastChild);
        } catch (\DOMException $e) {
        }


        // Save the modified XML file
        $xml->save($serviceXmlFile);

    }

    private function createEntity(InputInterface $input, OutputInterface $output): void
    {
        $entityFile = sprintf('%s/src/Entity/%s/%sEntity.php',
            $this->directory,
            $this->entityName,
            $this->entityName
        );

        $entity = str_replace(
            ['#namespace#', '#entityName#'],
            [$this->entityNamespace, $this->entityName],
            $this->entityTemplate
        );

        file_put_contents($entityFile, $entity);
    }

    private function createEntityHydrator(InputInterface $input, OutputInterface $output): void
    {
        $entityFile = sprintf('%s/src/Entity/%s/%sHydrator.php',
            $this->directory,
            $this->entityName,
            $this->entityName
        );

        $entity = str_replace(
            ['#namespace#', '#entityName#'],
            [$this->entityNamespace, $this->entityName],
            $this->entityTemplate
        );

        file_put_contents($entityFile, $entity);
    }
}
