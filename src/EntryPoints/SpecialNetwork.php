<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\Network;

use Html;
use HTMLForm;
use IncludableSpecialPage;
use MediaWiki\Extension\Network\EntryPoints\NetworkFunction;
use MediaWiki\Extension\Network\NetworkFunction\NetworkConfig;
use MediaWiki\MediaWikiServices;

class SpecialNetwork extends IncludableSpecialPage {

	public function __construct() {
		parent::__construct( 'Network' );
	}

	private $projects = [];
	private $people = [];

	/**
	 * @param string|null $subPage
	 */
	public function execute( $subPage ) {
		$request = $this->getRequest();
		$output = $this->getOutput();
		$this->setHeaders();

		/*
		$this->options = $GLOBALS['wgPageNetworkOptions'];
		$this->excludeTalkPages = (bool)$GLOBALS['wgPageNetworkExcludeTalkPages'];
		$this->excludedNamespaces = $GLOBALS['wgPageNetworkExcludedNamespaces'];
		$this->defaultEnableDisplayTitle = (bool)$GLOBALS['wgPageNetworkDefaultEnableDisplayTitle'];
		$this->defaultLabelMaxLength = (int)$GLOBALS['wgPageNetworkDefaultLabelMaxLength'];
		 */
		$width = $request->getInt( 'width' );
		if ( $width == 0 ) {
			$width = 800;
		}

		$height = $request->getInt( 'height' );
		if ( $height == 0 ) {
			$height = 800;
		}

		$statusarray = $request->getArray( 'status' );
		if ( !$statusarray ) {
			$size = 0;
		} else {
			$size = count( $statusarray );
		}
		if ( $size === 0 ) {
			$statusarray = [ 'open', 'stalled' ];
		} elseif ( $size === 1 ) {
			$statusarray = array_map( 'trim', explode( ',', $statusarray[0] ) );
		}

		$tasks = $request->getText( 'tasks' );
		if ( $tasks != '' ) {
			$taskarray = array_unique( array_map( 'trim', explode( ',', $tasks ) ) );
			foreach ( $taskarray as $i => $task ) {
				if ( $task[0] === 'T' ) {
					$taskarray[$i] = substr( $task, 1 );
				}
			}
			//$this->getTasks( $taskarray, $statusarray );
		} else {
			$taskarray = [];
		}

		$projects = $request->getText( 'projects' );
		if ( $projects != '' ) {
			$projectarray =
				array_unique( array_map( 'trim', explode( ',', $projects ) ) );
			//$this->getProjects( $projectarray, $statusarray );
			$mappedprojectarray = [];
			foreach ( $this->projects as $project ) {
				if ( in_array( $project['name'], $projectarray ) ) {
					$mappedprojectarray[] = $project['phid'];
				}
			}
			$projectarray = $mappedprojectarray;
		} else {
			$projectarray = [];
		}

		$nodearray = [];

		// get stuff

		if ( $nodearray != [] ) {
			$output->addModules( 'ext.PhabTaskGraph' );

			$graphName = 'PhabTaskGraphDiv';
			$data = [
				'id' => $graphName,
				'selected_tasks' => $taskarray,
				'selected_projects' => $projectarray,
				'nodes' => $nodearray,
				'links' => $linkarray,
				'projects' => $this->projects,
				'people' => $this->people,
				'url' => $GLOBALS['wgPhabTaskGraphPhabURL'],
				'width' => $width,
				'height' => $height
			];
			$output->addJsConfigVars( 'PhabTaskGraphConfig', $data );

			$config = new NetworkConfig();
			$parser = MediaWikiServices::getInstance()->getParser();
			$html = ( new NetworkFunction( $config ) )->handleParserFunctionCall( $parser, [] );
			$output->addHTML( $html );
		}

		if ( !$this->including() ) {

			$formDescriptor = [
				'tasksfield' => [
					'label-message' => 'phabtaskgraph-tasks-field-label',
					'help-message' => 'phabtaskgraph-tasks-field-help',
					'class' => 'HTMLTextField',
					'default' => $tasks,
					'name' => 'tasks'
				],
				'projectsfield' => [
					'label-message' => 'phabtaskgraph-projects-field-label',
					'help-message' => 'phabtaskgraph-projects-field-help',
					'class' => 'HTMLTextField',
					'default' => $projects,
					'name' => 'projects'
				],
				'statusfield' => [
					'label-message' => 'phabtaskgraph-status-field-label',
					'type' => 'multiselect',
					'options' => [
						$this->msg( 'phabtaskgraph-status-open' )->escaped() => 'open',
						$this->msg( 'phabtaskgraph-status-stalled' )->escaped() => 'stalled',
						$this->msg( 'phabtaskgraph-status-resolved' )->escaped() => 'resolved',
						$this->msg( 'phabtaskgraph-status-invalid' )->escaped() => 'invalid',
						$this->msg( 'phabtaskgraph-status-declined' )->escaped() => 'declined',
						$this->msg( 'phabtaskgraph-status-duplicate' )->escaped() => 'duplicate'
					],
					'default' => $statusarray,
					'name' => 'status'
				],
				'widthfield' => [
					'label-message' => 'phabtaskgraph-width-field-label',
					'help-message' => 'phabtaskgraph-width-field-help',
					'class' => 'HTMLTextField',
					'default' => $width,
					'name' => 'width'
				],
				'heightfield' => [
					'label-message' => 'phabtaskgraph-height-field-label',
					'help-message' => 'phabtaskgraph-height-field-help',
					'class' => 'HTMLTextField',
					'default' => $height,
					'name' => 'height'
				]
			];

			$htmlForm =
				HTMLForm::factory( 'ooui', $formDescriptor, $this->getContext() );

			$htmlForm->setMethod( 'get' );

			$htmlForm->prepareForm()->displayForm( false );
		}
	}
}
