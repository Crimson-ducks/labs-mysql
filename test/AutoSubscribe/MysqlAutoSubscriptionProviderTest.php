<?php
use Clearbooks\Labs\Bootstrap;
use Clearbooks\LabsMysql\AutoSubscribe\Entity\User;
use Clearbooks\LabsMysql\AutoSubscribe\MysqlAutoSubscriberProvider;
use Clearbooks\LabsMysql\AutoSubscribe\MysqlAutoSubscriptionProvider;
use Doctrine\DBAL\Connection;

/**
 * Created by PhpStorm.
 * User: Volodymyr
 * Date: 03/09/2015
 * Time: 13:15
 */
class MysqlAutoSubscriptionProviderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var AutoSubscriptionProvider
     */
    private $gateway;

    /**
     * @throws \Doctrine\DBAL\Exception\InvalidArgumentException
     */
    private function deleteAllSubscribers()
    {
        $this->connection->delete( '`subscribers`', [ '*' ] );
    }

    /**
     * @param User $user
     */
    private function addNewSubscriber( User $user )
    {
        $this->connection->insert( '`subscribers`', [ 'user_id' => $user->getId() ] );
    }

    /**
     * @param User[] $subscribers
     */
    protected function addNewSubscribersToDatabase( $subscribers )
    {
        foreach ( $subscribers as $subscriber ) {
            $this->addNewSubscriber( $subscriber );
        }
    }

    /**
     * @param User[] $initialSubscribers
     * @return bool
     */
    private function validateSubscribersDatabase( $initialSubscribers )
    {
        $actualSubscribers = ( new MysqlAutoSubscriberProvider( $this->connection ) )->getSubscribers();
        $this->assertEquals( $initialSubscribers, $actualSubscribers );
    }

    public function setUp()
    {
        parent::setUp();

        $this->connection = Bootstrap::getInstance()->getDIContainer()
            ->get( Connection::class );

        $this->connection->beginTransaction();
        $this->connection->setRollbackOnly();

        $this->gateway = new MysqlAutoSubscriptionProvider( $this->connection );
    }

    public function tearDown()
    {
        parent::tearDown();
        $this->connection->rollBack();
    }

    /**
     * @test
     */
    public function duringConstructionOfGateway_GatewayIsNotNull()
    {
        $this->assertNotNull( $this->gateway );
    }

    /**
     * @test
     */
    public function givenNoSubscribers_duringIsSubscribedAttempt_ReturnsFalse()
    {
        $response = $this->gateway->isSubscribed( new User( "test" ) );
        $this->assertFalse( $response );
    }

    /**
     * @test
     */
    public function givenExistentSubscriber_duringIsSubscribedAttempt_ReturnsTrue()
    {
        $theChosenOne = new User( "YES HE IS THE ONE" );
        $this->addNewSubscriber( $theChosenOne );

        $response = $this->gateway->isSubscribed( $theChosenOne );

        $this->assertTrue( $response );
    }

    /**
     * @test
     */
    public function givenExistentSubscribers_duringIsSubscribedAttempt_ReturnsTrueAndDoesNotEffectOtherSubscribers()
    {
        $theChosenOne = new User( "YES HE IS THE ONE" );
        $subscribers = [ new User( "Brolli" ), new User( "test2" ), $theChosenOne ];

        $this->addNewSubscribersToDatabase( $subscribers );

        $response = $this->gateway->isSubscribed( $theChosenOne );

        $this->assertTrue( $response );
        $this->validateSubscribersDatabase( $subscribers );
    }

    /**
     * @test
     */
    public function givenNoChosenSubscriber_duringUpdateSubscriptionAttemptAndTrueGiven_NewUserWillBeAddedToSubscribers()
    {
        $theChosenOne = new User( "YES HE IS THE ONE" );
        $subscribers = [ new User( "Brolli" ), new User( "test1" ), new User( "test2" ) ];

        $this->addNewSubscribersToDatabase( $subscribers );

        $subscribers [] = $theChosenOne;

        $this->gateway->updateSubscription( $theChosenOne, true );

        $this->validateSubscribersDatabase( $subscribers );
    }

    /**
     * @test
     */
    public function givenNoChosenSubscriber_duringUpdateSubscriptionAttemptAndFalseGiven_NoNewSubscribersWillBeCreatedNorDeleted()
    {
        $theChosenOne = new User( "YES HE IS THE ONE" );
        $subscribers = [ new User( "Brolli" ), new User( "test1" ), new User( "test2" ) ];

        $this->addNewSubscribersToDatabase( $subscribers );

        $this->gateway->updateSubscription( $theChosenOne, false );

        $this->validateSubscribersDatabase( $subscribers );
    }

    /**
     * @test
     */
    public function givenExistentChosenSubscribers_duringUpdateSubscriptionAttemptAndTrueGiven_NoSubscribersWillBeCreatedNorDeleted()
    {
        $theChosenOne = new User( "YES HE IS THE ONE" );
        $subscribers = [ new User( "Brolli" ), new User( "test1" ), new User( "test2" ), $theChosenOne ];

        $this->addNewSubscribersToDatabase( $subscribers );

        $this->gateway->updateSubscription( $theChosenOne, true );

        $this->validateSubscribersDatabase( $subscribers );
    }

    /**
     * @test
     */
    public function givenExistentChosenSubscribers_duringUpdateSubscriptionAttemptAndFalseGiven_ChosenSubscriberWillBeDeletedFromSubscribers()
    {
        $theChosenOne = new User( "YES HE IS THE ONE" );
        $subscribers = [ new User( "Brolli" ), new User( "test1" ), new User( "test2" ), $theChosenOne ];

        $this->addNewSubscribersToDatabase( $subscribers );

        array_pop( $subscribers );

        $this->gateway->updateSubscription( $theChosenOne, false );

        $this->validateSubscribersDatabase( $subscribers );
    }
}
