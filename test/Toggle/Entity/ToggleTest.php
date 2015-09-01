<?php
/**
 * Created by PhpStorm.
 * User: ryan
 * Date: 01/09/15
 * Time: 09:31
 */

namespace Clearbooks\LabsMysql\Toggle\Entity;


class ToggleTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var array $emptyToggleData
     */
    private $emptyToggleData;

    public function setUp()
    {
        parent::setUp();
        $this->emptyToggleData = array(
            'toggleName' => '',
            'id' => '',
            'isActive' => false,
            'screenshotUrl' => '',
            'toggleDesc' => '',
            'functionalityDes' => '',
            'reasonWhyDesc' => '',
            'locationDesc' => '',
            'guideUrl' => '',
            'notificationCopy' => ''
        );;
    }

    /**
     * @test
     */
    public function givenNoData_AllGettersDefaultCorrectly()
    {
        $toggle = new Toggle( '', '', '', '', '', '', '', '', '', '' );
        $this->assertToggleGettersMatchExpectedToggleData( $toggle, $this->emptyToggleData );
    }

    /**
     * @test
     */
    public function givenMinimumData_AllUninitialisedGettersDefaultCorrectly()
    {
        $toggleData = $this->emptyToggleData;
        $toggleData['toggleName'] = 'Toggle Name';
        $toggleData['id'] = 'Eye Dee';
        $toggle = new Toggle( $toggleData['toggleName'], $toggleData['id'] );
        $this->assertToggleGettersMatchExpectedToggleData( $toggle, $toggleData );
    }

    public function givenAllDataSet_AllGettersReturnCorrectValues()
    {
        $toggleData = array(
            'toggleName' => 'Toggle Name',
            'id' => 'Eye Dee',
            'isActive' => true,
            'screenshotUrl' => 'www.screenshot.fake',
            'toggleDesc' => 'this is a toggle',
            'functionalityDes' => 'it is used to toggle stuff',
            'reasonWhyDesc' => 'this is so that you can toggle things',
            'locationDesc' => 'it can be found in labs',
            'guideUrl' => 'www.guide.url',
            'notificationCopy' => 'oh look a new toggle!'
        );
        $toggle = new Toggle( $toggleData['toggleName'], $toggleData['id'], $toggleData['isActive'], $toggleData['screenshotUrl'], $toggleData['toggleDesc'], $toggleData['functionalityDes'], $toggleData['reasonWhyDesc'], $toggleData['locationDesc'], $toggleData['guideUrl'], $toggleData['notificationCopy'] );
        $this->assertToggleGettersMatchExpectedToggleData( $toggle, $toggleData );
    }

    /**
     * @param Toggle $toggle
     * @param $expectedToggleData
     */
    public function assertToggleGettersMatchExpectedToggleData( Toggle $toggle, $expectedToggleData )
    {
        $this->assertEquals( $expectedToggleData[ 'toggleName' ], $toggle->getName() );
        $this->assertEquals( $expectedToggleData[ 'id' ], $toggle->getRelease() );
        $this->assertEquals( $expectedToggleData[ 'isActive' ], $toggle->isActive() );
        $this->assertEquals( $expectedToggleData[ 'screenshotUrl' ], $toggle->getScreenshotUrl() );
        $this->assertEquals( $expectedToggleData[ 'toggleDesc' ], $toggle->getDescriptionOfToggle() );
        $this->assertEquals( $expectedToggleData[ 'functionalityDes' ], $toggle->getDescriptionOfFunctionality() );
        $this->assertEquals( $expectedToggleData[ 'reasonWhyDesc' ], $toggle->getDescriptionOfImplementationReason() );
        $this->assertEquals( $expectedToggleData[ 'locationDesc' ], $toggle->getDescriptionOfLocation() );
        $this->assertEquals( $expectedToggleData[ 'guideUrl' ], $toggle->getGuideUrl() );
        $this->assertEquals( $expectedToggleData[ 'notificationCopy' ], $toggle->getAppNotificationCopyText() );
    }

}
