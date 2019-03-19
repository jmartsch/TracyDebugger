addFilterBox({
    suffix: "-captainhook-panel",
    target: {
        selector: "#tracy-debug-panel-CaptainHookPanel .tracy-inner",
        items: "tbody tr"
    },
    wrapper: {
        tag: "div",
        attrs: {
            id: "tracyCaptainHookFilterBoxWrap",
            class: "tracy-filterbox-wrap tracy-filterbox-titlebar-wrap"
        }
    },
    input: {
        attrs: {
            placeholder: "Find..."
        }
    },
    addTo: {
        selector: "#tracy-debug-panel-CaptainHookPanel .tracy-icons",
        position: "before"
    },
    inputDelay: 500,
    highlight: {
        style: "background: #ff9; color: #125eae;",
        minChar: 2
    },
    displays: {
        counter: {
            tag: "p",
            addTo: {
                selector: "#tracyCaptainHookFilterBoxWrap",
                position: "append"
            },
            attrs: {
                class: "tracy-filterbox-counter"
            },
            text: function () {
	            var text = "";

	            if(this.getFilter() !== "") {
		            var matches = this.countVisible(),
		            	total = this.countTotal();

	                text = matches ? "<span>" + matches + "</span>/" + total : "No match";
	            }

	            return text;
            }
        },
        clearButton: {
            tag: "span",
            addTo: {
                selector: "#tracyCaptainHookFilterBoxWrap",
                position: "append"
            },
            attrs: {
                class: "tracy-filterbox-clear",
                onclick: "var input = this.parentElement.querySelector('input'); input.getFilterBox().clearFilterBox(); input.focus();"
            },
            text: function () {
                return this.getFilter() ? "&times;" : "";
            }
        }
    },
    callbacks: {
        onReady: function () {
            var $target = this.getTarget(),
                $input = this.getInput();

            $input.addEventListener("input", function() {
                $target.setAttribute("data-filterbox-active", "1");
            });
        },
        afterFilter: function () {
            var filter = (this.getFilter() || "").trim(),
                matchingSelector = this.getMatchingSelector(filter),
                $target = this.getTarget(),
                $itemsToHide,
                $foundFiles,
                $file,
                $parent,
                $parents,
                $toggles = $target.querySelectorAll(".tracy-toggle"),
                displayAttr = "data-filterbox-display",
                visibleMode = "true",
                hiddenMode = "none";

            if(filter === "") {
                var $itemsToClean = $target.querySelectorAll("[" + displayAttr + "]");

                $target.setAttribute("data-filterbox-active", "0");

                for(var i = 0; i < $itemsToClean.length; i++) {
                    $itemsToClean[i].removeAttribute(displayAttr);
                }

                return false;
            }

            $itemsToHide = $target.querySelectorAll("table, tr, .tracy-toggle, .tracy-toggle + div");
            for(var i = 0; i < $itemsToHide.length; i++) {
                $itemsToHide[i].setAttribute(displayAttr, hiddenMode);
            }

            $foundFiles = $target.querySelectorAll(matchingSelector);
            for(var j = 0; j < $foundFiles.length; j++) {
                $file = $foundFiles[j];
                $parents = getParentsUntil($file, "#tracy-debug-panel-CaptainHookPanel .tracy-inner");

                $file.setAttribute(displayAttr, visibleMode);

                for(var k = 0; k < $parents.length; k++) {
                    $parents[k].setAttribute(displayAttr, visibleMode);
                }
            }

            for(var j = 0; j < $toggles.length; j++) {
	            var $toggle = $toggles[j],
	            	$section = $toggle.nextElementSibling;

	            $toggle.setAttribute(displayAttr, $section.querySelector("table[" + displayAttr + "='true']") ? visibleMode : hiddenMode);
            }

            // reposition panel so that if it's wider after filtering (expanding results), it won't be off the screen
            window.Tracy.Debug.panels['tracy-debug-panel-CaptainHookPanel'].reposition();
        }
    }
});