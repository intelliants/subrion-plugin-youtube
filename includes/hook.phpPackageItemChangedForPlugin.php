<?php
//##copyright##

if (iaView::REQUEST_HTML == $iaView->getRequestType())
{
	if (empty($data))
	{
		return;
	}

	$plugin = 'youtube';
	$field = 'youtube_video';
	$pages = array(
		'accounts' => array('view_member', 'profile'),
		'autos' => array('autos_add', 'autos_edit', 'autos_view'),
		'articles' => array('submit_article', 'edit_article'),
		'estates' => array('estate_submit', 'estate_edit'),
		'listings' => array('add_listing', 'edit_listing')
	);

	foreach ($data as $node)
	{
		$item = $node['item'];
		switch($node['action'])
		{
			case '+':
				if($iaDb->exists('`item` = :item AND `name` = :field_name', array('item' => $item, 'field_name' => $field), 'fields'))
				{
					$iaDb->update(array('status' => 'active'), "`name` = '$field' AND `item` = '$item'", null, 'fields');
				}
				else
				{
					$sql = sprintf("ALTER TABLE `%s%s` ADD `%s` VARCHAR(%d) NOT NULL DEFAULT ''", $iaCore->iaDb->prefix, $item, $field, 128);
					$iaDb->query($sql);

					$id = $iaDb->insert(array(
						'extras' => $plugin,
						'item' => $item,
						'name' => $field,
						'type' => 'text',
						'length' => 128,
						'status' => 'active'
					), false, 'fields');
					if (isset($pages[$item]))
					{
						$data = array();
						foreach ($pages[$item] as $page)
						{
							$data[] = array(
								'page_name' => $page,
								'field_id' => $id,
								'extras' => $plugin
							);
						}
						$iaDb->insert($data, false, 'fields_pages');
					}
				}
				break;

			case '-':
				$iaDb->update(array('status' => 'approval'), "`name` = '$field' && `item` = '$item'", null, 'fields');
				break;
		}
	}
}