<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns="http://www.w3.org/1999/xhtml" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:saa="http://schema.slothsoft.net/amber/amberdata" xmlns:sse="http://schema.slothsoft.net/savegame/editor">

    <xsl:import href="farah://slothsoft@amber/templates/ambermoon/editor/globals/dictionary" />
    <xsl:import href="farah://slothsoft@amber/templates/ambermoon/editor/globals/savegame" />
    <xsl:import href="farah://slothsoft@amber/templates/ambermoon/editor/globals/editor" />
    <xsl:import href="farah://slothsoft@amber/templates/ambermoon/editor/globals/picker" />

    <xsl:variable name="portraits" select="document('farah://slothsoft@amber/api/amberdata?infosetId=lib.portraits')/saa:amberdata/saa:portrait-list" />
    <xsl:variable name="member-count" select=".//*[@name='member-count']/@value" />

    <xsl:template match="sse:savegame[count(sse:archive[*]) = 2]">
        <xsl:variable name="selectedArchives" select="sse:archive[*]" />

        <form action="?user={$user}" method="POST" class="amber-editor amber-text">
            <input type="hidden" name="repository" value="{$repository}" />
            <input type="hidden" name="game" value="{$game}" />
            <input type="hidden" name="version" value="{$version}" />
            <input type="hidden" name="infosetId" value="{$infoset}" />
            <input type="hidden" name="archivePath" value="_" />

            <xsl:apply-templates select="sse:archive[@name='Party_data.sav']" mode="form-content" />
            <xsl:apply-templates select="sse:archive[@name='Party_char.amb']" mode="form-content" />

            <nav class="amber-editor__actions">
                <button name="action" type="submit" value="save">Speichern</button>
                <xsl:for-each select="$selectedArchives">
                    <button name="download" type="submit" value="{@path}">
                        <xsl:text>Download </xsl:text>
                        <span class="amber-text amber-text--green">
                            <xsl:value-of select="@name" />
                        </span>
                    </button>
                </xsl:for-each>
            </nav>
        </form>
    </xsl:template>

    <xsl:template match="sse:archive[@name='Party_char.amb']" mode="form-content">
        <div class="amber-editor__party">
            <xsl:for-each select="sse:file[position() &lt;= 6]">
                <xsl:variable name="isVisible" select="not($member-count) or position() &lt;= $member-count" />
                <fieldset>
                    <xsl:if test="not($isVisible)">
                        <xsl:attribute name="disabled">disabled</xsl:attribute>
                    </xsl:if>
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

    <xsl:template match="sse:archive[@name='Party_data.sav']" mode="form-content">
        <xsl:for-each select="sse:file">
            <fieldset class="amber-editor__save">
                <xsl:for-each select=".//*[@name='member-count']">
                    <label class="amber-editor__member-count amber-text amber-text--orange">
                        <xsl:text>Anzahl der Gruppenmitglieder:</xsl:text>
                        <input type="range" min="1" max="6" list="member-count" value="{@value}" class="amber-text" data-editor-action="apply-member-count">
                            <xsl:apply-templates select="." mode="form-key" />
                        </input>
                    </label>
                    <datalist id="member-count">
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">6</option>
                        <option value="6">6</option>
                    </datalist>
                </xsl:for-each>
                <div class="amber-editor__hidden">
                    <div class="amber-editor__save-members">
                        <xsl:for-each select=".//*[@name='party-composition']//sse:select">
                            <xsl:apply-templates select="." mode="form-content" />
                        </xsl:for-each>
                    </div>
                    <div class="amber-editor__party-bits">
                        <input type="checkbox" checked="checked" />
                        <xsl:apply-templates select=".//*[@name='partybit-netsrak-absent']" mode="form-content" />
                        <xsl:apply-templates select=".//*[@name='partybit-mando-absent']" mode="form-content" />
                        <xsl:apply-templates select=".//*[@name='partybit-erik-absent']" mode="form-content" />
                        <xsl:apply-templates select=".//*[@name='partybit-chris-absent']" mode="form-content" />
                        <xsl:apply-templates select=".//*[@name='partybit-monika-absent']" mode="form-content" />
                    </div>
                </div>
            </fieldset>
        </xsl:for-each>
    </xsl:template>
</xsl:stylesheet>