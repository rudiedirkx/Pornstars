<script>
Array.prototype.invoke = function(method, ...args) { return this.forEach(el => el[method].call(el, ...args)), this; };
Array.prototype.prop = function(prop, value) { this.forEach(el => el[prop] = value); };

const $ = sel => document.querySelector(sel);
const $$ = sel => Array.from(document.querySelectorAll(sel));

$$('select[data-filter]').invoke('addEventListener', 'change', function(e) {
	if (!this.value) {
		return $$('.filterable').prop('hidden', false);
	}

	const selShow = this.dataset.filter.replace('?', this.value);
	$$('.filterable').forEach(sect => sect.hidden = !sect.querySelector(selShow));
}).forEach(el => el.dispatchEvent(new CustomEvent('change')));
</script>
