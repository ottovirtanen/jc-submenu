<?php
class DynamicItemsTest extends WP_UnitTestCase{

	var $importer;

	public function setUp(){
        parent::setUp();
        $this->submenu = $GLOBALS['jcsubmenu'];
    }

    function testElementDepths(){
        $walker = new JC_Submenu_Nav_Walker();

        $elements = array(
            (object)array(
                'menu_item_parent' => 0,
                'db_id' => 1,
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

        $output = $walker->set_elements_depth($elements);
        $this->assertEquals(8, count($elements));
        $this->assertEquals($elements[0]->depth, 0);
        $this->assertEquals($elements[1]->depth, 1);
        $this->assertEquals($elements[2]->depth, 1);
        $this->assertEquals($elements[3]->depth, 1);
        $this->assertEquals($elements[4]->depth, 2);
        $this->assertEquals($elements[5]->depth, 2);
        $this->assertEquals($elements[6]->depth, 3);
        $this->assertEquals($elements[7]->depth, 3);
    }

    function testElementState(){

        $walker = new JC_Submenu_Nav_Walker();

        /**
         * Top level parent
         */

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

        $output = $walker->_set_elements_state($elements);
        $elements[0]->current = 1;
        $elements[0]->split_section = 1;
        $this->assertEquals($elements, $output);

        /**
         * Second level parent
         */
        $elements = array(
            (object)array(
                'menu_item_parent' => 0,
                'db_id' => 1,
            ),
            // lvl 1
            (object)array(
                'menu_item_parent' => 1,
                'db_id' => 2,
                'current' => 1
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

        $output = $walker->_set_elements_state($elements);
        $elements[1]->current = 1;
        $elements[1]->split_section = 1;
        $elements[0]->classes = array('current-menu-parent', 'current-menu-ancestor');
        $elements[0]->current_item_parent = 1;
        $elements[0]->current_item_ancestor = 1;
        $this->assertEquals($elements, $output);

        /**
         * Third level parent
         */
        $elements = array(
            (object)array(
                'menu_item_parent' => 0,
                'db_id' => 1,
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
                'current' => 1
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

        $output = $walker->_set_elements_state($elements);
        $elements[4]->current = 1;
        $elements[4]->split_section = 1;
        $elements[0]->classes = array('current-menu-ancestor');
        $elements[0]->current_item_ancestor = 1;
        $elements[1]->classes = array('current-menu-parent', 'current-menu-ancestor');
        $elements[1]->current_item_parent = 1;
        $elements[1]->current_item_ancestor = 1;
        $this->assertEquals($elements, $output);

        /**
         * Fourth Level Parent
         */
        $elements = array(
            (object)array(
                'menu_item_parent' => 0,
                'db_id' => 1,
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
                'current' => 1
            ),
            (object)array(
                'menu_item_parent' => 6,
                'db_id' => 8,
            ),
            // no parent
            (object)array(
                'menu_item_parent' => 9,
                'db_id' => 9,
            ),
        );

        $output = $walker->_set_elements_state($elements);
        
        $elements[0]->classes = array('current-menu-ancestor');
        $elements[0]->current_item_ancestor = 1;
        $elements[1]->classes = array('current-menu-ancestor');
        $elements[1]->current_item_ancestor = 1;
        $elements[4]->classes = array('current-menu-parent', 'current-menu-ancestor');
        $elements[4]->current_item_parent = 1;
        $elements[4]->current_item_ancestor = 1;
        $elements[6]->current = 1;
        $elements[6]->split_section = 1;

        $this->assertEquals($elements, $output);
    }
}

?>