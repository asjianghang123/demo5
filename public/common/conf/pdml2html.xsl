<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" indent="yes" version="4.0"/>
<xsl:template match="/">
<html>
<head>
<title>Treeview</title>
<style type="text/css">
body
{
   margin:0 auto;padding:0;
   font-family:verdana,helvetica,arial,sans-serif;
   font-size: 12px;
   color:#333;
   background-color: #FAFAFA;
}
.IsVisible
{
   display: block;
}
.NotVisible
{
   display: none;
}
.Expander
{
   cursor: hand;
   font-family: Courier;
}
.Parent DIV
{
  /*margin-Left: 30px !important;*/
  margin-Left: 15px;
}

a:link {
	text-decoration: underline;
	color: #333;
}

a:visited {
	text-decoration: none;
	color: RED;
}

a:hover	{
	text-decoration: none;
	color: #333;
	background-color: #333;
}

a:active {
	text-decoration: none;
	color: #333;
}
</style>

<script language="JavaScript">

window.onload = OnPageLoaded;

function OnPageLoaded()
{
	ClearUnuseTreeIcon();
}

function ClearUnuseTreeIcon()
{
	var imgArray;
            
     imgArray = document.getElementsByTagName("IMG");        

  //imgArray = $("IMG"); 
  var link = imgArray[0].parentElement;
  var div = imgArray[0].parentElement.parentElement;
  for(var i = 0; imgArray.length > i; i++)
  {
  	var img = imgArray[i];
  	var link = img.parentElement;
  	var div = img.parentElement.parentElement;
  	if (link.tagName != "A" || div.tagName != "DIV")
  	{
  		continue;
  	}
  	var colChild = div.getElementsByTagName("DIV");
  	if(colChild.length == 0)
  	{
  		//link.href = "";
  		link.onclick=""
  		img.src = "../img/transparency.gif";
  	}
  }
}

function ExpanderClicked(obj)
{
	var ctlExpander=obj;
    var ctlSelectedEntry = obj.parentNode;
	var colChild=ctlSelectedEntry.getElementsByTagName("DIV");
    if(colChild.length > 0)
    {
        var strCSS;
        var ctlHidden = ctlSelectedEntry.getElementsByTagName("input");
		var ctlImg=ctlSelectedEntry.getElementsByTagName("IMG");
        if(ctlHidden[0].value == "0")
        {
			for(var imgCounter=0; ctlImg.length>imgCounter; imgCounter++)
			{
				if(ctlImg[imgCounter].src.search("transparency.gif") == -1)
				{ctlImg[imgCounter].src = "../img/tree_collapse.gif";}
			}
			strCSS = "IsVisible";
			for(var inputCounter=0; ctlHidden.length>inputCounter; inputCounter++)
			{
            	ctlHidden[inputCounter].value = "1";
			}
        }
        else
        {
			for(var imgCounter=0; ctlImg.length>imgCounter; imgCounter++)
			{
				if(ctlImg[imgCounter].src.search("transparency.gif") == -1)
				//if(ctlImg[imgCounter].src!="http://localhost/LTE/img/transparency.gif")
            	{ctlImg[imgCounter].src = "../img/tree_expand.gif";}
			}
            strCSS = "NotVisible";
			for(var inputCounter=0; ctlHidden.length>inputCounter; inputCounter++)
			{
            	ctlHidden[inputCounter].value = "0";
			}
        }
        //Show all the DIV elements that are direct children

        for(var intCounter = 0; colChild.length > intCounter; intCounter++)
        {
            colChild[intCounter].className = strCSS;
        }
    }
}
</script>

</head>
<body>
 <xsl:for-each select="*">
  <xsl:call-template name="GenerateTree">
   <xsl:with-param name="strCSS">Parent IsVisible</xsl:with-param>
  </xsl:call-template>
 </xsl:for-each>
</body>
</html>
</xsl:template>

<xsl:template name="GenerateTree">
	<xsl:param name="strCSS" />
	<xsl:choose>
		<xsl:when test="count(*) > 0">
			<div class="{$strCSS}" style="valign:center;white-space: nowrap">
			<xsl:choose>
				<xsl:when test="count(*) > 0">
				<input type="hidden" id="hidIsExpanded" value="1" />
				<a href="#"  onclick="ExpanderClicked(this)">
				<img style="margin-Top:1px;" border="0" src="../img/tree_collapse.gif"/>
				</a>
				</xsl:when>
				<xsl:otherwise>
					<img style="margin-Top:1px;" border="0" src="../img/transparency.gif"/>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:call-template name="GenerateTreeBuildNodeContent"/>
			<xsl:if test="count(*) > 0">
				<xsl:for-each select="*">
					<xsl:call-template name="GenerateTree">
					<xsl:with-param name="strCSS">IsVisible</xsl:with-param>
					</xsl:call-template>
				</xsl:for-each>
			</xsl:if>
			</div>
		</xsl:when>
		<xsl:otherwise>
			<xsl:choose>
                <xsl:when test="@hide='yes'">
                </xsl:when>  
                <xsl:otherwise>
                    <div  class="{$strCSS}" style="valign:center;white-space: nowrap">
                    <img style="margin-Top:1px;" border="0" src="../img/transparency.gif"/>
                    <xsl:call-template name="GenerateTreeBuildNodeContent"/>
                    </div>
                </xsl:otherwise>
			</xsl:choose>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>


<xsl:template name="GenerateTreeBuildNodeContent">	     
	<xsl:choose>
		<xsl:when test="string-length(@showname)>0">    
			<xsl:value-of select="@showname"/><br/>
		</xsl:when>
   		<xsl:otherwise>
    		<xsl:value-of select="@show"/><br/>
    	</xsl:otherwise>
    </xsl:choose>
</xsl:template>

</xsl:stylesheet>