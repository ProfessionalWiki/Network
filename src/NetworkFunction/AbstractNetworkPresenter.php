<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\Network\NetworkFunction;

use Html;

abstract class AbstractNetworkPresenter implements NetworkPresenter {

	private static int $idCounter = 1;

	protected string $html = '';

	public function setHtml( ResponseModel $viewModel ): void {
		$this->html =
			Html::element(
				'div',
				[
					'id' => 'network-viz-' . (string)self::$idCounter++,
					'class' => $viewModel->cssClass,
					'data-pages' => json_encode( $viewModel->pageNames ),
					'data-excludedpages' => json_encode( $viewModel->excludedPages ),
					'data-excludednamespaces' => json_encode( $viewModel->excludedNamespaces ),
					'data-options' => json_encode( $viewModel->visJsOptions ),
					'data-enabledisplaytitle' => json_encode( $viewModel->enableDisplayTitle ),
					'data-labelmaxlength' => json_encode( $viewModel->labelMaxLength ),
				]
			);
	}

	public function setTooManyPagesError(): void {
		// TODO: i18n
		$this->html = 'Too many pages. Can only show connections for up to 100 pages.';
	}

}
