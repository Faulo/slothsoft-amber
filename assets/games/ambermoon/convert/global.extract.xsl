<xsl:stylesheet version="1.0" 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:saa="http://schema.slothsoft.net/amber/amberdata"
	xmlns:sse="http://schema.slothsoft.net/savegame/editor"
	xmlns:str="http://exslt.org/strings"
	
	extension-element-prefixes="str">

	

	

	<xsl:template name="extract-spellbook">
		<xsl:param name="root" select="." />
		<xsl:for-each select=".//*[@name = 'spells']">
			<saa:spellbook>
				<xsl:for-each select=".//*[string-length(@value) &gt; 0]">
					<saa:spell-instance name="{saa:getName()}" />
				</xsl:for-each>
			</saa:spellbook>
		</xsl:for-each>
	</xsl:template>

	<xsl:template name="extract-equipment">
		<xsl:param name="root" select="." />
		<xsl:for-each select="$root//*[@name = 'equipment']">
			<saa:equipment>
				<xsl:for-each select="*">
					<xsl:call-template name="extract-slot" />
				</xsl:for-each>
			</saa:equipment>
		</xsl:for-each>
	</xsl:template>

	<xsl:template name="extract-inventory">
		<xsl:param name="root" select="." />
		<xsl:for-each select="$root//*[@name = 'inventory']">
			<saa:inventory>
				<xsl:for-each select="*">
					<xsl:call-template name="extract-slot" />
				</xsl:for-each>
			</saa:inventory>
		</xsl:for-each>
	</xsl:template>

	<xsl:template name="extract-slot">
		<xsl:param name="root" select="." />
		<saa:slot>
			<xsl:if test="$root/@name">
				<xsl:attribute name="name"><xsl:value-of select="@name" /></xsl:attribute>
			</xsl:if>
			<xsl:for-each select="$root//*[@name = 'item-id'][@value &gt; 0]/..">
				<xsl:call-template name="extract-item-instance" />
			</xsl:for-each>
		</saa:slot>
	</xsl:template>

	<xsl:template name="extract-item-instance">
		<xsl:param name="root" select="." />
		<saa:item-instance>
			<xsl:apply-templates select="$root//*[@name = 'item-id']" mode="attr" />
			<xsl:apply-templates select="$root//*[@name = 'item-amount']" mode="attr" />
			<xsl:apply-templates select="$root//*[@name = 'item-charge']" mode="attr" />

			<xsl:for-each select=".//*[@name = 'item-status']/*[@value != '']">
				<xsl:attribute name="is-{saa:getName()}" />
			</xsl:for-each>
		</saa:item-instance>
	</xsl:template>

	<xsl:template name="extract-class">
		<xsl:param name="root" />
		<xsl:param name="id" />
		<saa:class id="{$id}">
			<xsl:apply-templates select="$root//*[@name = 'name']" mode="attr" />
			<xsl:apply-templates select="$root//*[@name = 'school']" mode="attr" />
			<xsl:apply-templates select="$root//*[@name = 'apr-per-level']" mode="attr" />
			<xsl:apply-templates select="$root//*[@name = 'hp-per-level']" mode="attr" />
			<xsl:apply-templates select="$root//*[@name = 'sp-per-level']" mode="attr" />
			<xsl:apply-templates select="$root//*[@name = 'tp-per-level']" mode="attr" />
			<xsl:apply-templates select="$root//*[@name = 'slp-per-level']" mode="attr" />

			<xsl:apply-templates select="$root[../@name = 'class-experience']//sse:integer" mode="attr">
				<xsl:with-param name="name" select="'base-experience'" />
			</xsl:apply-templates>

			<xsl:for-each select="$root//*[@name = 'skills']/*">
				<saa:skill name="{saa:getName()}" maximum="{*/@value}" />
			</xsl:for-each>
		</saa:class>
	</xsl:template>

	<xsl:template name="extract-item">
		<xsl:param name="root" select="." />
		<xsl:param name="texts" select="/.." />
		<xsl:param name="id" />

		<saa:item id="{$id}">
			<xsl:variable name="type" select=".//*[@name = 'type']/@value" />
			<xsl:variable name="subtype" select=".//*[@name = 'subtype']/@value" />
			<xsl:variable name="subsubtype" select=".//*[@name = 'subsubtype']/@value" />
			<xsl:variable name="spell-id" select=".//*[@name = 'spell-id']/@value" />
			<xsl:variable name="spell-type" select=".//*[@name = 'spell-type']/@value" />
			<xsl:variable name="spell-dictionary">
				<xsl:choose>
					<xsl:when test="$spell-type = 0">
						<xsl:value-of select="'spells-white'" />
					</xsl:when>
					<xsl:when test="$spell-type = 1">
						<xsl:value-of select="'spells-blue'" />
					</xsl:when>
					<xsl:when test="$spell-type = 2">
						<xsl:value-of select="'spells-green'" />
					</xsl:when>
					<xsl:when test="$spell-type = 3">
						<xsl:value-of select="'spells-black'" />
					</xsl:when>
					<xsl:when test="$spell-type = 6">
						<xsl:value-of select="'spells-misc'" />
					</xsl:when>
				</xsl:choose>
			</xsl:variable>

			<xsl:apply-templates select=".//sse:integer[@name != ''] | .//sse:string" mode="attr" />

			<xsl:apply-templates select=".//*[@name = 'type']" mode="attr" />
			<xsl:apply-templates select=".//*[@name = 'hands']" mode="attr" />
			<xsl:apply-templates select=".//*[@name = 'fingers']" mode="attr" />
			<xsl:apply-templates select=".//*[@name = 'slot']" mode="attr" />
			<xsl:apply-templates select=".//*[@name = 'ammunition-type']" mode="attr" />
			<xsl:apply-templates select=".//*[@name = 'ranged-type']" mode="attr" />

			<xsl:if test=".//*[@name = 'attribute-value']/@value &gt; 0">
				<xsl:apply-templates select=".//*[@name = 'attribute-type']" mode="attr" />
			</xsl:if>
			<xsl:if test=".//*[@name = 'skill-value']/@value &gt; 0">
				<xsl:apply-templates select=".//*[@name = 'skill-type']" mode="attr" />
			</xsl:if>

			<xsl:if test="$spell-id &gt; 0">
				<xsl:attribute name="spell-type">
					<xsl:value-of select="key('string-dictionary', 'spell-types')[position() = $spell-type + 1]/@value" />
				</xsl:attribute>
				<xsl:attribute name="spell-name">
					<xsl:value-of select="key('string-dictionary', $spell-dictionary)[position() = $spell-id]/@value" />
				</xsl:attribute>
			</xsl:if>
			<xsl:attribute name="gender">
				<xsl:choose>
					<xsl:when test=".//*[@name = 'male']/@value &gt; .//*[@name = 'female']/@value">männlich</xsl:when>
					<xsl:when test=".//*[@name = 'male']/@value &lt; .//*[@name = 'female']/@value">weiblich</xsl:when>
					<xsl:otherwise>beide</xsl:otherwise>
				</xsl:choose>
			</xsl:attribute>
			<xsl:for-each select=".//*[@name = 'properties']/*[@value != '']">
				<xsl:attribute name="is-{saa:getName()}" />
			</xsl:for-each>
			<xsl:for-each select=".//*[@name = 'classes']/*[@value != '']">
				<saa:class name="{saa:getName()}" />
			</xsl:for-each>
			<xsl:if test="$type = 8">
				<xsl:call-template name="extract-text">
					<xsl:with-param name="root"
						select="$texts/*[position() = $subtype]/*/*[position() = ($subsubtype + 1)]//@value" />
					<xsl:with-param name="id" select="0" />
				</xsl:call-template>
			</xsl:if>
		</saa:item>
	</xsl:template>

	<xsl:template name="extract-text">
		<xsl:param name="root" select="." />
		<xsl:param name="id" />
		<saa:text id="{$id}">
			<xsl:for-each select="str:split($root, '^')">
				<xsl:value-of select="translate(., '$', '&#160;')" />
				<saa:br xmlns="http://www.w3.org/1999/xhtml" />
			</xsl:for-each>
		</saa:text>
	</xsl:template>




</xsl:stylesheet>