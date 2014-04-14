<?php
class SectionMenuTest extends WP_UnitTestCase{

	public function setUp(){
        parent::setUp();
        $this->submenu = $GLOBALS['jcsubmenu'];
    }

    function testSectionMenu(){

        $walker = new JC_Submenu_Nav_Walker(array('menu_item' => 5, 'menu_depth' => 1));

        $elements = array(
            (object)array(
                'menu_item_parent' => 0,
                'db_id' => 1,
                'current' => 1
            ),
            // lvl 1
            (object)array(
                'menu_item_parent' => 1,
                'db_id' => 2,
            ),
            (object)array(
                'menu_item_parent' => 1,
                'db_id' => 3,
            ),
            (object)array(
                'menu_item_parent' => 1,
                'db_id' => 4,
            ),
            // lvl 2
            (object)array(
                'menu_item_parent' => 2,
                'db_id' => 5,
            ),
            (object)array(
                'menu_item_parent' => 2,
                'db_id' => 6,
            ),
            // lvl 3
            (object)array(
                'menu_item_parent' => 5,
                'db_id' => 7,
            ),
            (object)array(
                'menu_item_parent' => 6,
                'db_id' => 8,
            ),
        );
    
        $elements = $walker->set_elements_depth($elements, 0, true);
        $output = $walker->_process_menu_section($elements);
        $this->assertEquals(1, count($output));
    }
}

?>