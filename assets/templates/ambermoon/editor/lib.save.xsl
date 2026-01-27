<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns="http://www.w3.org/1999/xhtml" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:saa="http://schema.slothsoft.net/amber/amberdata" xmlns:sse="http://schema.slothsoft.net/savegame/editor">

    <xsl:import href="farah://slothsoft@amber/templates/ambermoon/editor/globals/dictionary" />
    <xsl:import href="farah://slothsoft@amber/templates/ambermoon/editor/globals/savegame" />
    <xsl:import href="farah://slothsoft@amber/templates/ambermoon/editor/globals/editor" />
    <xsl:import href="farah://slothsoft@amber/templates/ambermoon/editor/globals/picker" />

    <xsl:template match="sse:archive[@name='Party_char.amb']" mode="form-content">
        <xsl:call-template name="picker.items" />
        <xsl:call-template name="picker.portraits" />

        <xsl:call-template name="savegame.tabs">
            <xsl:with-param name="label" select="'Aktiver Charakter:'" />
            <xsl:with-param name="options" select=".//*[@name = 'name']/@value" />
            <xsl:with-param name="list">
                <xsl:for-each select="sse:file">
                    <li>
                        <fieldset>
                            <xsl:call-template name="savegame.flex">
                                <xsl:with-param name="items">
                                    <xsl:call-template name="savegame.amber.events" />
                                    <div>
                                        <xsl:call-template name="savegame.amber.character-common" />
                                        <xsl:call-template name="savegame.amber.character-ailments" />
                                    </div>
                                    <div>
                                        <xsl:call-template name="savegame.amber.character-race" />
                                    </div>
                                    <div>
                                        <xsl:call-template name="savegame.amber.character-class" />
                                    </div>
                                    <div>
                                        <xsl:call-template name="savegame.amber.character-equipment" />
                                    </div>
                                    <div>
                                        <xsl:call-template name="savegame.amber.character-inventory" />
                                    </div>
                                    <div>
                                        <xsl:call-template name="savegame.amber.character-spells" />
                                    </div>
                                </xsl:with-param>
                            </xsl:call-template>
                        </fieldset>
                    </li>
                </xsl:for-each>
            </xsl:with-param>
        </xsl:call-template>
    </xsl:template>

    <xsl:template match="sse:archive[@name='Party_data.sav']" mode="form-content">
        <xsl:for-each select="sse:file">
            <xsl:call-template name="savegame.flex">
                <xsl:with-param name="items">
                    <xsl:for-each select="sse:group">
                        <div>
                            <xsl:call-template name="savegame.table">
                                <xsl:with-param name="label" select="@name" />
                                <xsl:with-param name="items">
                                    <xsl:apply-templates select="*" mode="item" />
                                </xsl:with-param>
                            </xsl:call-template>
                        </div>
                    </xsl:for-each>
                </xsl:with-param>
            </xsl:call-template>
            <xsl:for-each select=".//sse:instruction[@name = 'mob-existance']">
                <xsl:variable name="maps" select="key('dictionary-option', 'map-ids')" />
                <div>
                    <xsl:call-template name="savegame.tabs">
                        <xsl:with-param name="label" select="'Aktive Karte:'" />
                        <xsl:with-param name="optionTokens">
                            <xsl:for-each select="*">
                                <xsl:variable name="pos" select="position()" />
                                <xsl:if test="$maps[@key = $pos]">
                                    <token>
                                        <xsl:value-of select="$maps[@key = $pos]/@val" />
                                    </token>
                                </xsl:if>
                            </xsl:for-each>
                        </xsl:with-param>
                        <xsl:with-param name="list">
                            <xsl:for-each select="*">
                                <xsl:variable name="pos" select="position()" />
                                <xsl:if test="$maps[@key = $pos]">
                                    <li>
                                        <xsl:call-template name="savegame.flex">
                                            <xsl:with-param name="items">
                                                <xsl:apply-templates select="*" mode="item" />
                                            </xsl:with-param>
                                        </xsl:call-template>
                                    </li>
                                </xsl:if>
                            </xsl:for-each>
                        </xsl:with-param>
                    </xsl:call-template>
                </div>
            </xsl:for-each>
        </xsl:for-each>
    </xsl:template>

    <xsl:template match="sse:archive[@name='Merchant_data.amb']" mode="form-content">
        <xsl:call-template name="picker.items" />

        <xsl:variable name="fileList" select="sse:file" />
        <xsl:call-template name="savegame.tabs">
            <xsl:with-param name="label" select="'Aktiver Händler:'" />
            <xsl:with-param name="options" select="key('dictionary-option', 'shops')/@val" />
            <xsl:with-param name="list">
                <xsl:for-each select="key('dictionary-option', 'shops')/@key">
                    <xsl:for-each select="$fileList[number(@file-name) = current()]">
                        <li>
                            <xsl:call-template name="savegame.amber.shop" />
                        </li>
                    </xsl:for-each>
                </xsl:for-each>
            </xsl:with-param>
        </xsl:call-template>
    </xsl:template>

    <xsl:template match="sse:archive[@name='Chest_data.amb']" mode="form-content">
        <xsl:call-template name="picker.items" />

        <xsl:variable name="fileList" select="sse:file" />
        <xsl:call-template name="savegame.tabs">
            <xsl:with-param name="label" select="'Aktive Truhe:'" />
            <xsl:with-param name="options" select="$fileList/@file-name" />
            <xsl:with-param name="list">
                <xsl:for-each select="$fileList">
                    <li>
                        <xsl:call-template name="savegame.amber.shop" />
                    </li>
                </xsl:for-each>
            </xsl:with-param>
        </xsl:call-template>
    </xsl:template>
</xsl:stylesheet>