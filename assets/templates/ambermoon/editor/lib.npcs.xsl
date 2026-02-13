<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns="http://www.w3.org/1999/xhtml" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:saa="http://schema.slothsoft.net/amber/amberdata" xmlns:sse="http://schema.slothsoft.net/savegame/editor">

    <xsl:import href="farah://slothsoft@amber/templates/ambermoon/editor/globals/dictionary" />
    <xsl:import href="farah://slothsoft@amber/templates/ambermoon/editor/globals/savegame" />
    <xsl:import href="farah://slothsoft@amber/templates/ambermoon/editor/globals/editor" />
    <xsl:import href="farah://slothsoft@amber/templates/ambermoon/editor/globals/picker" />
    <xsl:import href="farah://slothsoft@amber/templates/ambermoon/editor/globals/portraits" />

    <xsl:template match="sse:archive[@name='NPC_char.amb']" mode="form-content">
        <xsl:call-template name="picker.portraits" />

        <xsl:call-template name="savegame.tabs">
            <xsl:with-param name="label" select="'Aktiver NPC:'" />
            <xsl:with-param name="options" select=".//*[@name = 'name']/@value" />
            <xsl:with-param name="list">
                <xsl:for-each select="sse:file">
                    <li>
                        <fieldset class="amber-editor__fieldset">
                            <xsl:call-template name="savegame.flex">
                                <xsl:with-param name="items">
                                    <xsl:call-template name="savegame.amber.events" />
                                    <div>
                                        <xsl:call-template name="savegame.amber.character-common" />
                                    </div>
                                    <div>
                                        <xsl:call-template name="savegame.amber.character-race" />
                                    </div>
                                    <div>
                                        <xsl:call-template name="savegame.amber.character-class" />
                                    </div>
                                </xsl:with-param>
                            </xsl:call-template>
                        </fieldset>
                    </li>
                </xsl:for-each>
            </xsl:with-param>
        </xsl:call-template>
    </xsl:template>
</xsl:stylesheet>