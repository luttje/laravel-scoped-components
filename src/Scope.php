<?php

namespace Luttje\ScopedComponents;

class Scope
{
    private static $currentScriptId = 0;

    /**
     * Injects PHP that starts a new scope.
     */
    public static function getStartScope()
    {
        // Used to generate a random id for reference later
        $currentScriptId = ++self::$currentScriptId;

        return <<<SCRIPT_ECHO
            <div>
                <?php
                \$tagConfigs = [
                    //[
                    //   'tag' => 'noscript'
                    //],
                    [
                        'tag' => 'template',
                        'attributes' => [
                            'style' => 'display:none',
                            'id' => 'scoped-element-$currentScriptId',
                        ]
                    ],
                ];
                foreach (\$tagConfigs as \$k => \$tagConfig) {
                    extract(\$tagConfig);

                    echo "<\$tag";

                    if(isset(\$attributes)) {
                        foreach (\$attributes as \$attribute => \$value) {
                            echo " \$attribute=\"\$value\"";
                        }
                    }

                    echo ">";
                ?>
            SCRIPT_ECHO;
    }

    public static function getEndScope()
    {
        $currentScriptId = self::$currentScriptId;

        return <<<SCRIPT_ECHO
            <?php
                echo "</\$tag>";
            } ?>

            <script data-reexecute-on-livewire-update>
                (function(cacheBreaker) {
                    const templateEl = document.getElementById('scoped-element-$currentScriptId');
                    const parentEl = templateEl.parentNode;
                    const content = templateEl.content.cloneNode(true);

                    const shadow = parentEl.shadowRoot || parentEl.attachShadow({ mode:"open" });
                    shadow.innerHTML = '';
                    shadow.append(content);

                    let comp = templateEl.closest('[wire\\\\3A id]');
                    if(comp !== null && comp.__livewire !== undefined) {
                        console.log(comp.__livewire);
                        comp.__livewire.initialize();
                    }

                    if(typeof Alpine !== 'undefined')
                        Alpine.initTree(shadow);
                    else
                        document.addEventListener('alpine:init', () => {
                            Alpine.initTree(shadow);
                        });
                })(<?php echo time(); ?>);
            </script>
        </div>
        SCRIPT_ECHO;
    }
}
