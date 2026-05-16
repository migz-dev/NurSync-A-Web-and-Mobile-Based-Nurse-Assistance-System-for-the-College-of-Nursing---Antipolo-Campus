<div id="modalCreateIO" class="hidden fixed inset-0 z-50">
  <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" data-modal-close></div>

  <div class="relative mx-auto my-10 w-[95%] max-w-5xl rounded-2xl bg-white shadow-2xl border border-slate-200">
    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 bg-slate-50 rounded-t-2xl">
      <h3 class="text-lg font-bold text-slate-900">Add Intake &amp; Output</h3>
      <button type="button" class="h-9 w-9 inline-flex items-center justify-center rounded-lg hover:bg-slate-100" data-modal-close>
        <i data-lucide="x" class="h-5 w-5"></i>
      </button>
    </div>

    <form method="POST" action="{{ route('faculty.chartings.io.store', $patient->id) }}" class="p-6 space-y-5">
      @csrf

      {{-- 2-column layout: left = I&O form, right = calculator --}}
      <div class="grid gap-6 md:grid-cols-[minmax(0,1.35fr)_minmax(0,1fr)]">
        {{-- LEFT: I&O FORM --}}
        <div class="space-y-4">
          <div class="grid gap-4 md:grid-cols-2">
            <div>
              <label class="block text-xs font-semibold text-slate-600 mb-1">Logged At *</label>
              <input name="logged_at" type="datetime-local" required
                     class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>
            <div>
              <label class="block text-xs font-semibold text-slate-600 mb-1">Remarks</label>
              <input id="ioRemarks" name="remarks" type="text"
                     class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                     placeholder="e.g., 8–12h shift, post-op day 1">
            </div>
          </div>

          {{-- Intake (per-source, optional) --}}
          <div class="rounded-xl border border-emerald-100 bg-emerald-50/40 p-4">
            <div class="text-sm font-semibold text-emerald-800 mb-3">Intake (mL)</div>
            <div class="grid gap-4 md:grid-cols-3">
              <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Oral</label>
                <input id="ioIntakeOral" name="intake_oral_ml" type="number" min="0"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                       placeholder="e.g., 240">
              </div>
              <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">IV</label>
                <input id="ioIntakeIV" name="intake_iv_ml" type="number" min="0"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                       placeholder="e.g., 500">
              </div>
              <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">NG / Tube</label>
                <input id="ioIntakeNG" name="intake_ng_ml" type="number" min="0"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                       placeholder="e.g., 60">
              </div>
            </div>

            <div class="mt-3">
              <label class="block text-xs font-semibold text-slate-600 mb-1">Total Intake (mL)</label>
              <input id="ioIntakeTotal" name="intake_ml" type="number" min="0"
                     class="w-full md:w-64 rounded-lg border border-slate-300 px-3 py-2 text-sm"
                     placeholder="Auto if left blank">
              <p class="mt-1 text-xs text-slate-500">
                Leave blank to auto-sum Oral + IV + NG in the system, or use the calculator on the right.
              </p>
            </div>
          </div>

          {{-- Output (per-source, optional) --}}
          <div class="rounded-xl border border-rose-100 bg-rose-50/40 p-4">
            <div class="text-sm font-semibold text-rose-800 mb-3">Output (mL)</div>
            <div class="grid gap-4 md:grid-cols-4">
              <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Urine</label>
                <input id="ioOutputUrine" name="output_urine_ml" type="number" min="0"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                       placeholder="e.g., 300">
              </div>
              <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Stool</label>
                <input id="ioOutputStool" name="output_stool_ml" type="number" min="0"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
              </div>
              <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Emesis</label>
                <input id="ioOutputEmesis" name="output_emesis_ml" type="number" min="0"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
              </div>
              <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Drain</label>
                <input id="ioOutputDrain" name="output_drain_ml" type="number" min="0"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
              </div>
            </div>

            <div class="mt-3">
              <label class="block text-xs font-semibold text-slate-600 mb-1">Total Output (mL)</label>
              <input id="ioOutputTotal" name="output_ml" type="number" min="0"
                     class="w-full md:w-64 rounded-lg border border-slate-300 px-3 py-2 text-sm"
                     placeholder="Auto if left blank">
              <p class="mt-1 text-xs text-slate-500">
                Leave blank to auto-sum Urine + Stool + Emesis + Drain in the system, or use the calculator on the right.
              </p>
            </div>
          </div>

          {{-- Net balance --}}
          <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1">Net Balance (mL)</label>
            <input id="ioNetBalance" name="net_balance_ml" type="number"
                   class="w-full md:w-64 rounded-lg border border-slate-300 px-3 py-2 text-sm"
                   placeholder="Intake − Output; auto if left blank">
            <p class="mt-1 text-xs text-slate-500">
              Positive = intake &gt; output, Negative = output &gt; intake.
            </p>
          </div>
        </div>

        {{-- RIGHT: I&O CALCULATOR SUMMARY --}}
        <aside class="rounded-2xl border border-slate-200 bg-slate-50/80 p-4 space-y-3">
          <div class="flex items-center justify-between">
            <div>
              <h4 class="text-sm font-semibold text-slate-900">Intake &amp; Output Calculator</h4>
              <p class="text-[11px] text-slate-500">
                Live summary based on per-source values.
              </p>
            </div>
            <i data-lucide="droplets" class="h-4 w-4 text-slate-400"></i>
          </div>

          <div class="space-y-3 text-xs">
            <div class="rounded-xl bg-white border border-emerald-100 px-3 py-2">
              <p class="text-[11px] font-semibold text-emerald-700">Total Intake (mL)</p>
              <p id="calcIoIntake" class="mt-1 text-lg font-semibold text-emerald-900">0 mL</p>
            </div>

            <div class="rounded-xl bg-white border border-rose-100 px-3 py-2">
              <p class="text-[11px] font-semibold text-rose-700">Total Output (mL)</p>
              <p id="calcIoOutput" class="mt-1 text-lg font-semibold text-rose-900">0 mL</p>
            </div>

            <div class="rounded-xl bg-white border border-slate-200 px-3 py-2">
              <p class="text-[11px] font-semibold text-slate-700">Net Balance (mL)</p>
              <p id="calcIoNet" class="mt-1 text-lg font-semibold text-slate-900">0 mL</p>
              <p id="calcIoNetTag" class="mt-0.5 text-[11px] text-slate-500">Balanced</p>
            </div>

            <div class="flex flex-wrap gap-2 pt-1">
              <button type="button"
                      id="btnIOApplyTotals"
                      class="inline-flex items-center rounded-lg bg-emerald-600 px-3 py-1.5 text-[11px] font-medium text-white hover:bg-emerald-700">
                Use totals in fields
              </button>
              <button type="button"
                      id="btnIOAppendNote"
                      class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-[11px] font-medium text-slate-700 hover:bg-slate-100">
                Append note to Remarks
              </button>
            </div>

            <p class="text-[10px] text-slate-400">
              Net = Intake − Output. Positive values indicate fluid gain; negative values indicate fluid loss.
            </p>
          </div>
        </aside>
      </div>

      <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-200">
        <button type="button" class="rounded-lg px-5 py-2 text-sm border border-slate-300 text-slate-700 hover:bg-slate-100" data-modal-close>
          Cancel
        </button>
        <button type="submit" class="rounded-lg px-5 py-2 text-sm bg-emerald-600 text-white hover:bg-emerald-700">
          Save
        </button>
      </div>
    </form>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const intakeOral   = document.getElementById('ioIntakeOral');
    const intakeIV     = document.getElementById('ioIntakeIV');
    const intakeNG     = document.getElementById('ioIntakeNG');
    const intakeTotal  = document.getElementById('ioIntakeTotal');

    const outputUrine  = document.getElementById('ioOutputUrine');
    const outputStool  = document.getElementById('ioOutputStool');
    const outputEmesis = document.getElementById('ioOutputEmesis');
    const outputDrain  = document.getElementById('ioOutputDrain');
    const outputTotal  = document.getElementById('ioOutputTotal');

    const netField     = document.getElementById('ioNetBalance');
    const remarksField = document.getElementById('ioRemarks');

    const calcIntakeEl = document.getElementById('calcIoIntake');
    const calcOutputEl = document.getElementById('calcIoOutput');
    const calcNetEl    = document.getElementById('calcIoNet');
    const calcNetTagEl = document.getElementById('calcIoNetTag');

    const btnApply     = document.getElementById('btnIOApplyTotals');
    const btnAppend    = document.getElementById('btnIOAppendNote');

    if (!calcIntakeEl || !calcOutputEl || !calcNetEl) return;

    function num(v) {
      const n = parseFloat(v);
      return isNaN(n) ? 0 : n;
    }

    function computeIo() {
      const intake =
        num(intakeOral?.value) +
        num(intakeIV?.value) +
        num(intakeNG?.value);

      const output =
        num(outputUrine?.value) +
        num(outputStool?.value) +
        num(outputEmesis?.value) +
        num(outputDrain?.value);

      const net = intake - output;

      calcIntakeEl.textContent = intake.toFixed(0) + ' mL';
      calcOutputEl.textContent = output.toFixed(0) + ' mL';
      calcNetEl.textContent    = net.toFixed(0) + ' mL';

      if (net > 0) {
        calcNetTagEl.textContent = 'Positive balance (fluid gain)';
      } else if (net < 0) {
        calcNetTagEl.textContent = 'Negative balance (fluid loss)';
      } else {
        calcNetTagEl.textContent = 'Balanced';
      }
    }

    const inputs = [
      intakeOral, intakeIV, intakeNG,
      outputUrine, outputStool, outputEmesis, outputDrain
    ].filter(Boolean);

    inputs.forEach(el => {
      ['input', 'change'].forEach(evt => {
        el.addEventListener(evt, computeIo);
      });
    });

    computeIo();

    // Apply totals into the Total & Net fields
    btnApply?.addEventListener('click', function () {
      const intakeText = calcIntakeEl.textContent || '0 mL';
      const outputText = calcOutputEl.textContent || '0 mL';
      const netText    = calcNetEl.textContent || '0 mL';

      const intake = parseFloat(intakeText) || 0;
      const output = parseFloat(outputText) || 0;
      const net    = parseFloat(netText)    || 0;

      if (intakeTotal)  intakeTotal.value  = intake.toFixed(0);
      if (outputTotal)  outputTotal.value  = output.toFixed(0);
      if (netField)     netField.value     = net.toFixed(0);
    });

    // Append short I&O summary to remarks
    btnAppend?.addEventListener('click', function () {
      if (!remarksField) return;
      const intakeText = calcIntakeEl.textContent || '0 mL';
      const outputText = calcOutputEl.textContent || '0 mL';
      const netText    = calcNetEl.textContent || '0 mL';

      const note = `I&O summary: Intake ${intakeText}, Output ${outputText}, Net ${netText}.`;
      if (!remarksField.value) {
        remarksField.value = note;
      } else {
        remarksField.value = remarksField.value.trim() + '\n' + note;
      }
    });
  });
</script>
