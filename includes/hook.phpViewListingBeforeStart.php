<?php
//##copyright##

if (iaView::REQUEST_HTML == $iaView->getRequestType())
{
	if (empty($item) || !$iaCore->get('youtube_items_enabled'))
	{
		return;
	}

	$enabled_items = explode(',', $iaCore->get('youtube_items_enabled'));

	if (empty($enabled_items))
	{
		return;
	}

	$field = 'youtube_video';

	if (in_array($item, $enabled_items))
	{
		if ($url = $iaDb->one($field, iaDb::convertIds($listing), $item))
		{
			if (preg_match('/^(http\:\/\/|https\:\/\/)?(www\.)?youtube\.com\/watch\?([\w=\-&]+&)?v=[\w\-]+((&|\?|#)[\w=\-&]+)?$/si', $url))
			{
				$url = parse_url($url);
				parse_str($url['query'], $url);

				$iaView->assign($field, $url['v']);
			}
			elseif (preg_match('/^(http\:\/\/|https\:\/\/)?(www\.)?youtu\.be\/[\w\-]+((&|\?|#)[\w=\-&]+)?$/si', $url))
			{
				$url = parse_url($url);
				$path = explode('/', $url['path']);

				$iaView->assign($field, $path[1]);
			}
			else
			{
				return;
			}
		}
	}
}