<?php

namespace Doctrine\Tests\ORM\Functional\Ticket;

/**
 * @group DDC-2759
 */
class DDC2759Test extends \Doctrine\Tests\OrmFunctionalTestCase
{
    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        parent::setUp();

        try {
            $this->schemaTool->createSchema(
                [
                $this->em->getClassMetadata(DDC2759Qualification::class),
                $this->em->getClassMetadata(DDC2759Category::class),
                $this->em->getClassMetadata(DDC2759QualificationMetadata::class),
                $this->em->getClassMetadata(DDC2759MetadataCategory::class),
                ]
            );
        } catch(\Exception $e) {
            return;
        }

        $qualification = new DDC2759Qualification();
        $qualificationMetadata = new DDC2759QualificationMetadata($qualification);

        $category1 = new DDC2759Category();
        $category2 = new DDC2759Category();

        $metadataCategory1 = new DDC2759MetadataCategory($qualificationMetadata, $category1);
        $metadataCategory2 = new DDC2759MetadataCategory($qualificationMetadata, $category2);

        $this->em->persist($qualification);
        $this->em->persist($qualificationMetadata);

        $this->em->persist($category1);
        $this->em->persist($category2);

        $this->em->persist($metadataCategory1);
        $this->em->persist($metadataCategory2);

        $this->em->flush();
        $this->em->clear();
    }

    public function testCorrectNumberOfAssociationsIsReturned()
    {
        $repository = $this->em->getRepository(DDC2759Qualification::class);

        $builder = $repository->createQueryBuilder('q')
            ->select('q, qm, qmc')
            ->innerJoin('q.metadata', 'qm')
            ->innerJoin('qm.metadataCategories', 'qmc');

        $result = $builder->getQuery()
            ->getArrayResult();

        self::assertCount(2, $result[0]['metadata']['metadataCategories']);
    }
}

/** @Entity  @Table(name="ddc_2759_qualification") */
class DDC2759Qualification
{
    /** @Id @Column(type="integer") @GeneratedValue */
    public $id;

    /** @OneToOne(targetEntity="DDC2759QualificationMetadata", mappedBy="content") */
    public $metadata;
}

/** @Entity  @Table(name="ddc_2759_category") */
class DDC2759Category
{
    /** @Id @Column(type="integer") @GeneratedValue */
    public $id;

    /** @OneToMany(targetEntity="DDC2759MetadataCategory", mappedBy="category") */
    public $metadataCategories;
}

/** @Entity  @Table(name="ddc_2759_qualification_metadata") */
class DDC2759QualificationMetadata
{
    /** @Id @Column(type="integer") @GeneratedValue */
    public $id;

    /** @OneToOne(targetEntity="DDC2759Qualification", inversedBy="metadata") */
    public $content;

    /** @OneToMany(targetEntity="DDC2759MetadataCategory", mappedBy="metadata") */
    protected $metadataCategories;

    public function __construct(DDC2759Qualification $content)
    {
        $this->content = $content;
    }
}

/** @Entity  @Table(name="ddc_2759_metadata_category") */
class DDC2759MetadataCategory
{
    /** @Id @Column(type="integer") @GeneratedValue */
    public $id;

    /** @ManyToOne(targetEntity="DDC2759QualificationMetadata", inversedBy="metadataCategories") */
    public $metadata;

    /** @ManyToOne(targetEntity="DDC2759Category", inversedBy="metadataCategories") */
    public $category;

    public function __construct(DDC2759QualificationMetadata $metadata, DDC2759Category $category)
    {
        $this->metadata = $metadata;
        $this->category = $category;
    }
}
