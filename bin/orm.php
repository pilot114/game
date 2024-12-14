<?php declare(strict_types=1);
include 'vendor/autoload.php';

use Cycle\Database;
use Cycle\Database\Config;
use Cycle\Schema;
use Cycle\Annotated;
use Cycle\Annotated\Locator\TokenizerEmbeddingLocator;
use Cycle\Annotated\Locator\TokenizerEntityLocator;
use Cycle\ORM;
use Cycle\ORM\EntityManager;
use Game\Entity\GameObjectTemplate;
use Game\Enum\GameObjectType;
use Spiral\Tokenizer\ClassLocator;
use Symfony\Component\Finder\Finder;

$dbal = new Database\DatabaseManager(
    new Config\DatabaseConfig([
        'default' => 'default',
        'databases' => [
            'default' => ['connection' => 'game']
        ],
        'connections' => [
            'game' => new Config\SQLiteDriverConfig(
                connection: new Config\SQLite\FileConnectionConfig(
                    database:  __DIR__ . '/../database.sqlite'
                ),
                queryCache: true,
            ),
        ]
    ])
);

$finder = (new Finder())->files()->in([__DIR__ . '/../src/Entity']);
$classLocator = new ClassLocator($finder);

$embeddingLocator = new TokenizerEmbeddingLocator($classLocator);
$entityLocator = new TokenizerEntityLocator($classLocator);

$schema = (new Schema\Compiler())->compile(new Schema\Registry($dbal), [
    new Schema\Generator\ResetTables(),             // Reconfigure table schemas (deletes columns if necessary)
    new Annotated\Embeddings($embeddingLocator),    // Recognize embeddable entities
    new Annotated\Entities($entityLocator),         // Identify attributed entities
    new Annotated\TableInheritance(),               // Setup Single Table or Joined Table Inheritance
    new Annotated\MergeColumns(),                   // Integrate table #[Column] attributes
    new Schema\Generator\GenerateRelations(),       // Define entity relationships
    new Schema\Generator\GenerateModifiers(),       // Apply schema modifications
    new Schema\Generator\ValidateEntities(),        // Ensure entity schemas adhere to conventions
    new Schema\Generator\RenderTables(),            // Create table schemas
    new Schema\Generator\RenderRelations(),         // Establish keys and indexes for relationships
    new Schema\Generator\RenderModifiers(),         // Implement schema modifications
    new Schema\Generator\ForeignKeys(),             // Define foreign key constraints
    new Annotated\MergeIndexes(),                   // Merge table index attributes
    new Schema\Generator\SyncTables(),              // Align table changes with the database
    new Schema\Generator\GenerateTypecast(),        // Typecast non-string columns
]);

$orm = new ORM\ORM(new ORM\Factory($dbal), new ORM\Schema($schema));

$em = new EntityManager($orm);

// write via EM
//$user = new User("Antony");
//$em->persist($user)->run();

// read via Repo

//$user = $orm->getRepository(User::class)->findByPK(1);
//dump($user->toArray());

$entities = [];
$entities[] = new GameObjectTemplate(
    name: 'Житель деревни',
    description: 'Обычный человек без оружия',
    type: GameObjectType::NPC,
);
$entities[] = new GameObjectTemplate(
    name: 'Зелье здоровья',
    description: 'Восстанавливает HP',
    type: GameObjectType::Item,
);
foreach ($entities as $entity) {
    $em->persist($entity)->run();
}