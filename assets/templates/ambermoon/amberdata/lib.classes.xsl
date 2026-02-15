<xsl:stylesheet version="1.0" xmlns="http://schema.slothsoft.net/amber/amberdata" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:saa="http://schema.slothsoft.net/amber/amberdata" xmlns:sse="http://schema.slothsoft.net/savegame/editor" xmlns:str="http://exslt.org/strings"
    extension-element-prefixes="str" xmlns:set="http://exslt.org/sets">

    <xsl:import href="farah://slothsoft@amber/templates/ambermoon/amberdata/globals/dictionary" />
    <xsl:import href="farah://slothsoft@amber/templates/ambermoon/amberdata/globals/extract" />

    <xsl:template match="/*">
        <amberdata version="0.1">
            <xsl:apply-templates select="*/sse:savegame" />
        </amberdata>
    </xsl:template>

    <xsl:template match="sse:savegame">
        <xsl:variable name="expList" select="sse:archive[@type='AM2'][1]//*[@name='class-experience']/*" />
        <xsl:variable name="characters" select="sse:archive[@name='NPC_char.amb']/* | sse:archive[@name='Party_char.amb']/*" />

        <saa:race-list>
            <xsl:for-each select="set:distinct($characters//*[@name='race']/@value)">
                <xsl:sort select="." data-type="number" />
                <xsl:variable name="race" select="." />
                <xsl:for-each select="($characters[.//*[@name='race']/@value = $race])[1]">
                    <xsl:call-template name="extract-race">
                        <xsl:with-param name="root" select="." />
                        <xsl:with-param name="include-current" select="false()" />
                    </xsl:call-template>
                </xsl:for-each>
            </xsl:for-each>
        </saa:race-list>

        <xsl:if test="count($expList)">
            <saa:class-list>
                <xsl:for-each select="$expList">
                    <xsl:variable name="class" select="position() - 1" />
                    <xsl:variable name="exp" select="." />
                    <xsl:for-each select="($characters[.//*[@name='class']/@value = $class])[1]">
                        <xsl:call-template name="extract-class">
                            <xsl:with-param name="id" select="$class" />
                            <xsl:with-param name="root" select="." />
                            <xsl:with-param name="base-experience" select="$exp" />
                        </xsl:call-template>
                    </xsl:for-each>
                </xsl:for-each>
            </saa:class-list>
        </xsl:if>
    </xsl:template>
</xsl:stylesheet>