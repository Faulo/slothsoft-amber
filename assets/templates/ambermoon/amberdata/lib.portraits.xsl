<xsl:stylesheet version="1.0" xmlns="http://schema.slothsoft.net/amber/amberdata" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:saa="http://schema.slothsoft.net/amber/amberdata" xmlns:sse="http://schema.slothsoft.net/savegame/editor">

    <xsl:import href="farah://slothsoft@amber/templates/ambermoon/amberdata/globals/dictionary" />
    <xsl:import href="farah://slothsoft@amber/templates/ambermoon/amberdata/globals/extract" />
    <xsl:import href="farah://slothsoft@amber/templates/ambermoon/amberdata/globals/static" />

    <xsl:template match="/*">
        <amberdata version="0.1">
            <xsl:apply-templates select="*/sse:savegame" />
        </amberdata>
    </xsl:template>

    <xsl:template match="sse:savegame">
        <xsl:variable name="characters" select="sse:archive[@name='NPC_char.amb' or @name='Party_char.amb']/*" />

        <portrait-instance-list>
            <xsl:for-each select="$characters">
                <xsl:sort select=".//*[@name = 'portrait-id']/@value" data-type="number" />
                <portrait-instance>
                    <xsl:apply-templates select=".//*[@name = 'portrait-id']" mode="attr">
                        <xsl:with-param name="name" select="'id'" />
                    </xsl:apply-templates>
                    <xsl:apply-templates select=".//*[@name = 'name']" mode="attr">
                        <xsl:with-param name="name" select="'character'" />
                    </xsl:apply-templates>
                    <xsl:apply-templates select=".//*[@name = 'race']" mode="attr" />
                    <xsl:apply-templates select=".//*[@name = 'gender']" mode="attr" />
                </portrait-instance>
            </xsl:for-each>
        </portrait-instance-list>

        <xsl:copy-of select="$static/saa:portrait-list" />

        <gfx-list>
            <xsl:for-each select="$static//saa:portrait">
                <gfx archive="Portraits.amb" group="portrait-id" id="{@id}" position="{@id - 1}" />
            </xsl:for-each>
        </gfx-list>
    </xsl:template>
</xsl:stylesheet>