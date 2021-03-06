<?php

namespace PlentyConnector\tests\Unit\Adapter\ShopwareAdapter\ResponseParser\Customer;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PlentyConnector\Connector\TransferObject\Order\Customer\Customer;
use PlentyConnector\tests\Unit\Adapter\ShopwareAdapter\ResponseParser\ResponseParserTest;
use Shopware\Models\Customer\Group;
use Shopware\Models\Newsletter\Address;
use ShopwareAdapter\ResponseParser\Address\AddressResponseParser;
use ShopwareAdapter\ResponseParser\Customer\CustomerResponseParser;

/**
 * Class CustomerResponseParserTest
 *
 * @group ResponseParser
 */
class CustomerResponseParserTest extends ResponseParserTest
{
    /**
     * @var AddressResponseParser
     */
    private $responseParser;

    protected function setUp()
    {
        parent::setUp();

        $customerGroup = $this->createMock(Group::class);
        $customerGroup->expects($this->any())->method('getId')->willReturn(1);

        $groupRepository = $this->createMock(EntityRepository::class);
        $groupRepository->expects($this->any())->method('findOneBy')->with(['key' => 'H'])->willReturn($customerGroup);

        $address = $this->createMock(Address::class);
        $address->expects($this->any())->method('getAdded')->willReturn(new \DateTime());

        $newsletterRepository = $this->createMock(EntityRepository::class);
        $newsletterRepository->expects($this->any())->method('findOneBy')->with(['email' => 'mustermann@b2b.de'])->willReturn($address);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->at(0))->method('getRepository')->willReturn($groupRepository);
        $entityManager->expects($this->at(1))->method('getRepository')->willReturn($newsletterRepository);

        /**
         * @var AddressResponseParser $parser
         */
        $this->responseParser = new CustomerResponseParser($this->identityService, $entityManager);
    }

    public function testCustomerParsing()
    {
        /**
         * @var Customer $customer
         */
        $customer = $this->responseParser->parse(self::$orderData['customer']);

        self::assertNull($customer->getBirthday());
        self::assertSame(Customer::TYPE_NORMAL, $customer->getType());
        self::assertSame('mustermann@b2b.de', $customer->getEmail());
        self::assertSame('Händler', $customer->getFirstname());
        self::assertSame('Kundengruppe-Netto', $customer->getLastname());
        self::assertTrue($customer->getNewsletter());
        self::assertSame('20003', $customer->getNumber());
        self::assertSame(Customer::SALUTATION_MR, $customer->getSalutation());
        self::assertNull($customer->getTitle());
    }
}
