<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns="http://www.w3.org/1999/xhtml" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:saa="http://schema.slothsoft.net/amber/amberdata" xmlns:sse="http://schema.slothsoft.net/savegame/editor">

    <xsl:import href="farah://slothsoft@amber/templates/ambermoon/editor/globals/dictionary" />
    <xsl:import href="farah://slothsoft@amber/templates/ambermoon/editor/globals/savegame" />
    <xsl:import href="farah://slothsoft@amber/templates/ambermoon/editor/globals/editor" />
    <xsl:import href="farah://slothsoft@amber/templates/ambermoon/editor/globals/picker" />

    <xsl:variable name="portraits" select="document('farah://slothsoft@amber/api/amberdata?infosetId=lib.portraits')/saa:amberdata/saa:portrait-list" />

    <xsl:template match="sse:archive[@name='Party_char.amb']" mode="form-content">
        <div class="amber-editor__party">
            <xsl:for-each select="sse:file[position() &lt;= 6]">
                <fieldset>
                    <div>
                        <xsl:apply-templates select=".//*[@name = 'name']" mode="form-content">
                            <xsl:with-param name="additionalClasses" select="'amber-text--yellow'" />
                        </xsl:apply-templates>
                    </div>
                    <xsl:for-each select=".//*[@name = 'portrait-id']">
                        <xsl:variable name="value" select="@value" />
                        <div>
                            <xsl:apply-templates select="." mode="portrait-picker" />
                            <xsl:for-each select="$portraits">
                                <select class="amber-editor__input amber-editor__input--widget amber-text" data-editor-action="apply-portrait">
                                    <xsl:for-each select="*">
                                        <optgroup label="{@name}">
                                            <xsl:for-each select="*">
                                                <option value="{@id}">
                                                    <xsl:if test="@id = $value">
                                                        <xsl:attribute name="selected">selected</xsl:attribute>
                                                    </xsl:if>
                                                    <xsl:value-of select="concat('Portrait #', @id)" />
                                                </option>
                                            </xsl:for-each>
                                        </optgroup>
                                    </xsl:for-each>
                                </select>
                            </xsl:for-each>
                        </div>
                    </xsl:for-each>
                    <div>
                        <xsl:apply-templates select=".//*[@name = 'race']" mode="form-content">
                            <xsl:with-param name="additionalClasses" select="'amber-text--green'" />
                            <xsl:with-param name="editorAction" select="'apply-race'" />
                        </xsl:apply-templates>
                        <!-- <xsl:call-template name="savegame.table"> <xsl:with-param name="label" select="'Rasse'" /> <xsl:with-param name="items"> <xsl:apply-templates select=".//*[@name = 'race']" mode="item" /> <xsl:apply-templates select=".//*[@name = 'age']" mode="item" /> </xsl:with-param> </xsl:call-template> 
                            <xsl:for-each select=".//*[@name = 'attributes']"> <xsl:call-template name="savegame.table"> <xsl:with-param name="label" select="'Attribute'" /> <xsl:with-param name="class" select="'attributes'" /> <xsl:with-param name="items"> <xsl:apply-templates select="*" mode="item" /> </xsl:with-param> </xsl:call-template> 
                            </xsl:for-each> <xsl:apply-templates select=".//*[@name = 'languages']" mode="item" /> -->
                    </div>
                    <div>
                        <xsl:apply-templates select=".//*[@name = 'gender']" mode="form-content" />
                    </div>
                    <div>
                        <span data-name="age">
                            <label>
                                <xsl:text>Alter:</xsl:text>
                                <xsl:apply-templates select=".//*[@name = 'age']/*[1]" mode="form-content">
                                    <xsl:with-param name="additionalClasses" select="'amber-editor__input--right'" />
                                    <xsl:with-param name="size" select="3" />
                                </xsl:apply-templates>
                            </label>
                            <xsl:text>/</xsl:text>
                            <xsl:apply-templates select=".//*[@name = 'age']/*[2]" mode="form-content">
                                <xsl:with-param name="additionalClasses" select="'amber-editor__input--left amber-text--green'" />
                                <xsl:with-param name="readonly" select="true()" />
                                <xsl:with-param name="size" select="3" />
                            </xsl:apply-templates>
                        </span>
                    </div>
                    <div>
                        <span data-name="hit-points">
                            <xsl:variable name="hp" select=".//*[@name = 'hp-per-level']/@value" />
                            <label>
                                <xsl:text>LP:</xsl:text>
                                <xsl:apply-templates select=".//*[@name = 'hp-per-level']" mode="form-content">
                                    <xsl:with-param name="additionalClasses" select="'amber-text--blue'" />
                                    <xsl:with-param name="readonly" select="true()" />
                                </xsl:apply-templates>
                            </label>
                            <xsl:for-each select=".//*[@name = 'hit-points']">
                                <xsl:apply-templates select="*[@name='current']" mode="form-hidden">
                                    <xsl:with-param name="value" select="$hp" />
                                </xsl:apply-templates>
                                <xsl:apply-templates select="*[@name='maximum']" mode="form-hidden">
                                    <xsl:with-param name="value" select="$hp" />
                                </xsl:apply-templates>
                                <xsl:apply-templates select="*[@name='maximum-mod']" mode="form-hidden">
                                    <xsl:with-param name="value" select="0" />
                                </xsl:apply-templates>
                            </xsl:for-each>
                        </span>
                        <xsl:text> </xsl:text>
                        <span data-name="spell-points">
                            <xsl:variable name="sp" select=".//*[@name = 'sp-per-level']/@value" />
                            <label>
                                <xsl:text>SP:</xsl:text>
                                <xsl:apply-templates select=".//*[@name = 'sp-per-level']" mode="form-content">
                                    <xsl:with-param name="additionalClasses" select="'amber-text--blue'" />
                                    <xsl:with-param name="readonly" select="true()" />
                                </xsl:apply-templates>
                            </label>
                            <xsl:for-each select=".//*[@name = 'spell-points']">
                                <xsl:apply-templates select="*[@name='current']" mode="form-hidden">
                                    <xsl:with-param name="value" select="$sp" />
                                </xsl:apply-templates>
                                <xsl:apply-templates select="*[@name='maximum']" mode="form-hidden">
                                    <xsl:with-param name="value" select="$sp" />
                                </xsl:apply-templates>
                                <xsl:apply-templates select="*[@name='maximum-mod']" mode="form-hidden">
                                    <xsl:with-param name="value" select="0" />
                                </xsl:apply-templates>
                            </xsl:for-each>
                        </span>
                    </div>
                    <div>
                        <label>
                            <xsl:text>TP:</xsl:text>
                            <xsl:apply-templates select=".//*[@name = 'tp-per-level']" mode="form-content">
                                <xsl:with-param name="additionalClasses" select="'amber-text--blue'" />
                                <xsl:with-param name="readonly" select="true()" />
                            </xsl:apply-templates>
                        </label>
                        <xsl:apply-templates select=".//*[@name = 'training-points']" mode="form-hidden">
                            <xsl:with-param name="value" select=".//*[@name = 'tp-per-level']/@value" />
                        </xsl:apply-templates>
                        <xsl:text> </xsl:text>
                        <label>
                            <xsl:text>SLP:</xsl:text>
                            <xsl:apply-templates select=".//*[@name = 'slp-per-level']" mode="form-content">
                                <xsl:with-param name="additionalClasses" select="'amber-text--blue'" />
                                <xsl:with-param name="readonly" select="true()" />
                            </xsl:apply-templates>
                        </label>
                        <xsl:apply-templates select=".//*[@name = 'spelllearn-points']" mode="form-hidden">
                            <xsl:with-param name="value" select=".//*[@name = 'slp-per-level']/@value" />
                        </xsl:apply-templates>
                    </div>
                    <div>
                        <label>
                            <xsl:text>APR pro Level:</xsl:text>
                            <xsl:apply-templates select=".//*[@name = 'apr-per-level']" mode="form-content">
                                <xsl:with-param name="additionalClasses" select="'amber-text--blue'" />
                                <xsl:with-param name="readonly" select="true()" />
                            </xsl:apply-templates>
                        </label>
                        <xsl:apply-templates select=".//*[@name = 'experience']" mode="form-hidden">
                            <xsl:with-param name="value" select="0" />
                        </xsl:apply-templates>
                        <xsl:apply-templates select=".//*[@name = 'level']" mode="form-hidden">
                            <xsl:with-param name="value" select="1" />
                        </xsl:apply-templates>
                        <xsl:apply-templates select=".//*[@name = 'attacks-per-round']" mode="form-hidden">
                            <xsl:with-param name="value" select="1" />
                        </xsl:apply-templates>
                    </div>
                    <div>
                        <xsl:apply-templates select=".//*[@name = 'class']" mode="form-content">
                            <xsl:with-param name="additionalClasses" select="'amber-text--blue'" />
                            <xsl:with-param name="editorAction" select="'apply-class'" />
                        </xsl:apply-templates>
                    </div>
                    <div class="amber-editor__languages">
                        <xsl:apply-templates select=".//*[@name = 'languages']" mode="form-content" />
                    </div>
                    <xsl:for-each select=".//*[@name = 'attributes']">
                        <div>
                            <table class="amber-editor__attributes attributes">
                                <caption class="amber-text amber-text--green">Attribute</caption>
                                <tbody>
                                    <xsl:for-each select="*">
                                        <tr>
                                            <td>
                                                <xsl:apply-templates select="." mode="form-name" />
                                            </td>
                                            <td>
                                                <xsl:apply-templates select="*[1]" mode="form-content">
                                                    <xsl:with-param name="additionalClasses" select="'amber-editor__input--right amber-text--orange'" />
                                                    <xsl:with-param name="readonly" select="true()" />
                                                </xsl:apply-templates>
                                            </td>
                                            <td>
                                                <xsl:apply-templates select="*[3]" mode="form-hidden">
                                                    <xsl:with-param name="value" select="0" />
                                                </xsl:apply-templates>
                                                <xsl:text>/</xsl:text>
                                            </td>
                                            <td>
                                                <xsl:apply-templates select="*[2]" mode="form-content">
                                                    <xsl:with-param name="additionalClasses" select="'amber-editor__input--right amber-text--green'" />
                                                    <xsl:with-param name="readonly" select="true()" />
                                                </xsl:apply-templates>
                                            </td>
                                        </tr>
                                    </xsl:for-each>
                                </tbody>
                            </table>
                        </div>
                    </xsl:for-each>
                    <xsl:for-each select=".//*[@name = 'skills']">
                        <div>
                            <table class="amber-editor__skills skills">
                                <caption class="amber-text amber-text--blue">Fähigkeiten</caption>
                                <tbody>
                                    <xsl:for-each select="*">
                                        <tr>
                                            <td>
                                                <xsl:apply-templates select="." mode="form-name" />
                                            </td>
                                            <td>
                                                <xsl:apply-templates select="*[1]" mode="form-content">
                                                    <xsl:with-param name="additionalClasses" select="'amber-editor__input--right amber-text--orange'" />
                                                    <xsl:with-param name="readonly" select="true()" />
                                                </xsl:apply-templates>
                                            </td>
                                            <td>
                                                <xsl:apply-templates select="*[3]" mode="form-hidden">
                                                    <xsl:with-param name="value" select="0" />
                                                </xsl:apply-templates>
                                                <xsl:text>/</xsl:text>
                                            </td>
                                            <td>
                                                <xsl:apply-templates select="*[2]" mode="form-content">
                                                    <xsl:with-param name="additionalClasses" select="'amber-editor__input--right amber-text--blue'" />
                                                    <xsl:with-param name="readonly" select="true()" />
                                                </xsl:apply-templates>
                                            </td>
                                        </tr>
                                    </xsl:for-each>
                                </tbody>
                            </table>
                        </div>
                    </xsl:for-each>
                    <div>
                        <button type="button" data-editor-action="roll-character" class="amber-editor__input amber-editor__input--widget amber-text amber-text--orange">Neu Würfeln</button>

                        <xsl:for-each select=".//*[@name = 'hand' or @name = 'finger' or @name = 'finger' or @name = 'attack' or @name = 'defense' or @name = 'magic-attack' or @name = 'magic-defense' or @name = 'gold' or @name = 'food' or @name = 'weight']">
                            <xsl:apply-templates select="." mode="form-hidden">
                                <xsl:with-param name="value" select="0" />
                            </xsl:apply-templates>
                        </xsl:for-each>

                        <xsl:for-each select=".//*[@name = 'equipment' or @name = 'inventory']">
                            <xsl:apply-templates select="*/*" mode="form-hidden">
                                <xsl:with-param name="value" select="0" />
                            </xsl:apply-templates>
                        </xsl:for-each>

                        <div class="amber-editor__hidden">
                            <xsl:for-each select=".//*[@name = 'ailments']">
                                <div data-name="ailments">
                                    <xsl:apply-templates select="*" mode="form-content">
                                        <xsl:with-param name="value" select="0" />
                                    </xsl:apply-templates>
                                </div>
                            </xsl:for-each>
                            <xsl:for-each select=".//*[@name = 'spellbooks']">
                                <div data-name="spellbooks">
                                    <xsl:apply-templates select="*" mode="form-content" />
                                </div>
                            </xsl:for-each>
                            <xsl:for-each select=".//*[@name = 'spells']">
                                <div data-name="spells">
                                    <xsl:apply-templates select="*/*" mode="form-content">
                                        <xsl:with-param name="value" select="0" />
                                    </xsl:apply-templates>
                                </div>
                            </xsl:for-each>
                        </div>
                    </div>
                </fieldset>
            </xsl:for-each>
        </div>
    </xsl:template>

    <xsl:template match="nothing">
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
</xsl:stylesheet>