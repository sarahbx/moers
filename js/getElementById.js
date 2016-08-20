/*
Following IE getElementById script by J. Max Wilson found here:
http://www.sixteensmallstones.org/ie-javascript-bugs-overriding-internet-explorers-documentgetelementbyid-to-be-w3c-compliant-exposes-an-additional-bug-in-getattributes
*/
if (/msie/i.test (navigator.userAgent)) //only override IE
{
	document.nativeGetElementById = document.getElementById;
	document.getElementById = function(id)
	{
		var elem = document.nativeGetElementById(id);
		if(elem)
		{
			//make sure that it is a valid match on id
			if(elem.attributes['id'].value == id)
			{
				return elem;
			}
			else
			{
				//otherwise find the correct element
				for(var i=1;i<document.all[id].length;i++)
				{
					if(document.all[id][i].attributes['id'].value == id)
					{
						return document.all[id][i];
					}
				}
			}
		}
		return null;
	};
}
