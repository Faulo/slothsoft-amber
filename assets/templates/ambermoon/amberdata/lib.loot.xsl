<xsl:stylesheet version="1.0" xmlns="http://schema.slothsoft.net/amber/amberdata" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:saa="http://schema.slothsoft.net/amber/amberdata" xmlns:sse="http://schema.slothsoft.net/savegame/editor">

    <xsl:import href="farah://slothsoft@amber/templates/ambermoon/amberdata/globals/dictionary" />
    <xsl:import href="farah://slothsoft@amber/templates/ambermoon/amberdata/globals/extract" />

    <xsl:template match="/*">
        <amberdata version="0.1">
            <xsl:apply-templates select="*/sse:savegame" />
        </amberdata>
    </xsl:template>

    <xsl:template match="sse:savegame">
        <xsl:variable name="merchants" select="sse:archive[@name='Merchant_data.amb']/*" />
        <xsl:variable name="chests" select="sse:archive[@name='Chest_data.amb']/*" />
        <saa:merchant-list>
            <xsl:for-each select="$merchants">
                <xsl:call-template name="extract-merchant" />
            </xsl:for-each>
        </saa:merchant-list>
        <saa:chest-list>
            <xsl:for-each select="$chests">
                <xsl:call-template name="extract-chest" />
            </xsl:for-each>
        </saa:chest-list>
    </xsl:template>

    <xsl:template name="extract-merchant">
        <xsl:param name="root" select="." />
        <xsl:param name="id" select="@file-name" />
        <saa:merchant id="{$id}" name="{key('dictionary-option', 'shops')[@key = number($id)]/@val}">
            <xsl:for-each select="$root//*[@name = 'inventory']/*/*">
                <xsl:call-template name="extract-slot" />
            </xsl:for-each>
        </saa:merchant>
    </xsl:template>

    <xsl:template name="extract-chest">
        <xsl:param name="root" select="." />
        <xsl:param name="id" select="@file-name" />
        <saa:chest id="{$id}">
            <xsl:apply-templates select="$root//*[@name = 'gold']" mode="attr" />
            <xsl:apply-templates select="$root//*[@name = 'food']" mode="attr" />
            <xsl:for-each select="$root//*[@name = 'inventory']/*/*">
                <xsl:call-template name="extract-slot" />
            </xsl:for-each>
        </saa:chest>
    </xsl:template>
</xsl:stylesheet>