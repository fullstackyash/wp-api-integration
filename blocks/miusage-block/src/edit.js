/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';

import { PanelBody, ToggleControl} from '@wordpress/components';

const { useEffect, useState } = wp.element;

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './editor.scss';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {Element} Element to render.
 */
export default function Edit({attributes, setAttributes}) {
	const { showId, showFirstName, showLastName, showEmail, showDate } = attributes;
	const [ apiData, setApiData ] = useState([]);

	/**
	 * Collects data from API and prepares response to display.
	 */
	function getApiData() {
		var request = new XMLHttpRequest();
		var params = `action=get_api_data&_ajaxnonce=${ajaxload_params.nonce}`;
		request.open("POST", ajaxload_params.ajax_url, true);
		request.setRequestHeader(
		"Content-Type",
		"application/x-www-form-urlencoded; charset=UTF-8"
		);
		request.onload = function ajaxLoad() {
		if (request.status >= 200 && request.status < 400) {
			let apiData = JSON.parse(request.responseText);
					setApiData(apiData?.data?.rows);
		}
		};

		request.send(params);
  	}

	/**
	 * UseEffect which runs once on every page refresh or when the block is
	 * added.
	 */
	useEffect(() => {
		getApiData();
	}, []);

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Block Settings', 'miusage-block' ) }>
					<ToggleControl
						checked={ !! showId }
						label={ __(
								'Show ID',
								'miusage-block'
						) }
						onChange={ () =>
							setAttributes( {
								showId: ! showId,
							} )
						}
					/>
					<ToggleControl
						checked={ !! showFirstName }
						label={ __(
								'Show First Name',
								'miusage-block'
						) }
						onChange={ () =>
							setAttributes( {
								showFirstName: ! showFirstName,
							} )
						}
					/>
					<ToggleControl
						checked={ !! showLastName }
						label={ __(
								'Show Last Name',
								'miusage-block'
						) }
						onChange={ () =>
							setAttributes( {
								showLastName: ! showLastName,
							} )
						}
					/>
					<ToggleControl
						checked={ !! showEmail }
						label={ __(
								'Show Email',
								'miusage-block'
						) }
						onChange={ () =>
							setAttributes( {
								showEmail: ! showEmail,
							} )
						}
					/>
					<ToggleControl
						checked={ !! showDate }
						label={ __(
								'Show Date',
								'miusage-block'
						) }
						onChange={ () =>
							setAttributes( {
								showDate: ! showDate,
							} )
						}
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...useBlockProps() } className="wp-block-create-block-miusage-block">
				{apiData && Object.keys(apiData).length > 0 && (
					<div className="miusage-table">
						<div className="scrollable-div">
							<div className="table-headers row">
								{showId && (
									<div className="fieldset">ID</div>
								)}	
								{showFirstName && (
									<div className="fieldset">First Name</div>
								)}
								{showLastName && (
									<div className="fieldset">Last Name</div>
								)}
								{showEmail && (	
									<div className="fieldset">Email</div>
								)}	
								{showDate && (	
									<div className="fieldset">Date</div>
								)}	
							</div>
							<div className="table-details">
								{Object.keys(apiData).map(key => (
									<div className="records row" key={key}>
										{showId && (
											<div className="fieldset">{apiData[key].id}</div>
										)}
										{showFirstName && (
											<div className="fieldset">{apiData[key].fname}</div>
										)}
										{showLastName && (
											<div className="fieldset">{apiData[key].lname}</div>
										)}	
										{showEmail && (
											<div className="fieldset">{apiData[key].email}</div>
										)}	
										{showDate && (
											<div className="fieldset">{new Date(apiData[key].date * 1000).toLocaleDateString()}</div>
										)}
									</div>
								))}
							</div>
						</div>
					</div>
				)}
			</div>
		</>
	);
}