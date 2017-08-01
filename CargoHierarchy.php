<?php
/**
* CargoHierarchy - holds the tree for hierarchy inorder to implement Nested Set model for
* efficient storage and query of hierarchy fields.
*
* @author Feroz Ahmad
* @ingroup Cargo
*/

class CargoHierarchy {
	public $mTitle;
	public $mChildren;
	public $mLeft = 0;
	public $mRight = 0;

	function __construct( $curTitle = '__pseudo_root__' ) {
		$this->mTitle = $curTitle;
		$this->mChildren = array();
	}

	function addChild( $child ) {
		$this->mChildren[] = $child;
	}

	/**
	* Turn a manually-created "structure", defined as a bulleted list
	* in wikitext, into a tree. This code has been borrowed from PFTree class
	* of Page Forms Extension
	*/
	public static function newFromWikiText( $wikitext ) {
		// A dummy node (__pseudo_root__ is added so that
		// multiple nodes can be added in the first level
		$fullTree = new CargoHierarchy();
		$lines = explode( "\n", $wikitext );
		foreach ( $lines as $line ) {
			$numBullets = 0;
			for ( $i = 0; $i < strlen( $line ) && $line[$i] == '*'; $i++ ) {
				$numBullets++;
			}
			if ( $numBullets == 0 ) continue;
			$lineText = trim( substr( $line, $numBullets ) );
			$curParentNode = $fullTree->getLastNodeForLevel( $numBullets );
			$curParentNode->addChild( new CargoHierarchy( $lineText ) );
		}
		return $fullTree;
	}

	function getLastNodeForLevel( $level ) {
		if ( $level <= 1 || count( $this->mChildren ) == 0 ) {
			return $this;
		}
		$lastNodeOnCurLevel = end( $this->mChildren );
		return $lastNodeOnCurLevel->getLastNodeForLevel( $level - 1 );
	}

	function generateHierarchyStructureTableData() {
		$tableData = array();
		$this->computeLeftRight();
		//  Preorder traversal using Stack data structure
		$stack = new SplStack();
		$stack->push( $this );
		while( !$stack->isEmpty() ) {
			$node = $stack->pop();
			$row = array();
			$row['_value'] = $node->mTitle;
			$row['_left'] = $node->mLeft;
			$row['_right'] = $node->mRight;
			$tableData[] = $row;
			foreach( array_reverse( $node->mChildren ) as $child ) {
				$stack->push( $child );
			}
		}
		return $tableData;
	}

	private function computeLeftRight( &$counter = 1 ) {
		$this->mLeft = $counter;
		$counter += 1;
		//  Visit mChildren of the current node
		foreach( $this->mChildren as $child ) {
			$child->computeLeftRight( $counter );
		}
		$this->mRight = $counter;
		$counter += 1;
	}
}