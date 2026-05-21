<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Distribution Agreement — Pasaporte a tu Salud</title>
    <link
        href="https://fonts.googleapis.com/css2?family=EB+Garamond:ital,wght@0,400;0,600;1,400&family=Montserrat:wght@400;600;700&display=swap"
        rel="stylesheet" />
    <style>
        :root {
            --navy: #111111;
            --gold: #2b2b2b;
            --gold-light: #aeaeae;
            --cream: #ffffff;
            --ink: #000000;
            --muted: #7a7a7a;
            --rule: #dcdcdc;
            --accent-bg: #f5f5f5
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0 }

        body { font-family: Arial, Helvetica, sans-serif; background: var(--cream); color: var(--ink); font-size: 15px; line-height: 1.8 }

        .cover { background: var(--navy); color: #fff; padding: 60px 80px 50px; position: relative; overflow: hidden }
        .cover::before { content: ""; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: repeating-linear-gradient(45deg, transparent, transparent 40px, rgba(255,255,255,.02) 40px, rgba(255,255,255,.02) 41px) }
        .cover-inner { position: relative; z-index: 1; max-width: 860px; margin: 0 auto }
        .cover-label { font-family: "Montserrat", sans-serif; font-size: 10px; font-weight: 700; letter-spacing: 4px; text-transform: uppercase; color: var(--gold-light); margin-bottom: 18px }
        .cover h1 { font-family: "EB Garamond", serif; font-size: 38px; font-weight: 600; line-height: 1.2; margin-bottom: 8px; color: #fff }
        .cover-subtitle { font-family: "Montserrat", sans-serif; font-size: 12px; font-weight: 600; letter-spacing: 2px; color: var(--gold-light); text-transform: uppercase; margin-bottom: 40px }
        .cover-divider { width: 60px; height: 2px; background: var(--gold-light); margin-bottom: 32px }
        .cover-parties { display: grid; grid-template-columns: 1fr 1fr; gap: 32px; margin-top: 8px }
        .cover-party h3 { font-family: "Montserrat", sans-serif; font-size: 9px; font-weight: 700; letter-spacing: 3px; text-transform: uppercase; color: var(--gold-light); margin-bottom: 6px }
        .cover-party p { font-size: 14px; color: rgba(255,255,255,.75); line-height: 1.5 }

        .page { max-width: 860px; margin: 0 auto; padding: 0 40px 80px }

        .caratula-section { background: #fff; border: 1px solid var(--rule); border-top: 4px solid var(--gold); margin: 48px 0 0; border-radius: 2px; overflow: hidden }
        .caratula-header { background: var(--navy); color: #fff; padding: 20px 32px; display: flex; align-items: center; gap: 16px }
        .caratula-header h2 { font-family: "Montserrat", sans-serif; font-size: 13px; font-weight: 700; letter-spacing: 3px; text-transform: uppercase; color: var(--gold-light) }
        .caratula-table { width: 100%; border-collapse: collapse }
        .caratula-table tr:nth-child(even) { background: var(--accent-bg) }
        .caratula-table td { padding: 11px 24px; border-bottom: 1px solid var(--rule); vertical-align: top; font-size: 14px }
        .caratula-table td:first-child { font-family: "Montserrat", sans-serif; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--muted); width: 38%; padding-top: 13px }
        .badge { display: inline-block; background: var(--accent-bg); border: 1px solid var(--rule); font-family: "Montserrat", sans-serif; font-size: 10px; font-weight: 600; letter-spacing: 1px; padding: 3px 10px; border-radius: 2px; color: var(--muted); font-style: italic }

        .annexes-nav { margin: 36px 0; background: var(--navy); border-radius: 2px; padding: 24px 28px }
        .annexes-nav h3 { font-family: "Montserrat", sans-serif; font-size: 9px; font-weight: 700; letter-spacing: 3px; text-transform: uppercase; color: var(--gold-light); margin-bottom: 16px }
        .annexes-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px }
        .annexes-grid a { font-family: "Montserrat", sans-serif; font-size: 10px; font-weight: 600; color: rgba(255,255,255,.6); text-decoration: none; padding: 8px 10px; border: 1px solid rgba(255,255,255,.1); border-radius: 2px; transition: all .2s; display: flex; gap: 8px; align-items: flex-start }
        .annexes-grid a:hover { border-color: var(--gold-light); color: #fff; background: rgba(255,255,255,.06) }
        .annexes-grid a span.num { color: var(--gold-light); flex-shrink: 0 }

        .section-head { margin: 60px 0 28px; padding-bottom: 14px; border-bottom: 1px solid var(--rule); position: relative }
        .section-head::after { content: ""; position: absolute; bottom: -1px; left: 0; width: 48px; height: 2px; background: var(--gold) }
        .section-head .label { font-family: "Montserrat", sans-serif; font-size: 9px; font-weight: 700; letter-spacing: 3px; text-transform: uppercase; color: var(--gold); margin-bottom: 6px }
        .section-head h2 { font-size: 24px; font-weight: 600; color: var(--navy) }

        .clause { margin-bottom: 36px; scroll-margin-top: 20px }
        .clause-title { font-family: "Montserrat", sans-serif; font-size: 11px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; color: var(--navy); padding: 10px 0; border-top: 1px solid var(--rule); display: flex; align-items: center; gap: 12px }
        .clause-num { background: var(--navy); color: var(--gold-light); font-size: 9px; font-family: "Montserrat", sans-serif; font-weight: 700; padding: 3px 8px; border-radius: 2px; letter-spacing: 1px }
        .clause-body { padding: 14px 0 0; font-size: 14.5px; line-height: 1.85; color: var(--ink) }
        .clause-body p { margin-bottom: 12px }
        .clause-body ul, .clause-body ol { padding-left: 24px; margin: 12px 0 }
        .clause-body li { margin-bottom: 8px }

        .obl-item { display: flex; gap: 14px; padding: 10px 14px; border-radius: 2px; margin-bottom: 6px }
        .obl-item:nth-child(odd) { background: var(--accent-bg) }
        .obl-item .obl-key { font-family: "Montserrat", sans-serif; font-size: 9px; font-weight: 700; letter-spacing: 1px; color: var(--gold-light); min-width: 22px; padding-top: 3px; text-transform: uppercase }
        .obl-item .obl-val { font-size: 14px; line-height: 1.7 }

        .callout { padding: 18px 22px; border-radius: 2px; margin: 16px 0; font-size: 14px; line-height: 1.7 }
        .callout.warning { background: #f0f0f0; border-left: 4px solid #555; color: #2b2b2b }
        .callout.info { background: #f5f5f5; border-left: 4px solid #111; color: #111 }
        .callout.danger { background: #e8e8e8; border-left: 4px solid #111; color: #111 }
        .callout strong { font-family: "Montserrat", sans-serif; font-size: 10px; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase; display: block; margin-bottom: 6px }

        .price-table { width: 100%; border-collapse: collapse; margin: 18px 0; font-size: 14px }
        .price-table thead tr { background: var(--navy); color: var(--gold-light) }
        .price-table thead th { font-family: "Montserrat", sans-serif; font-size: 9px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; padding: 12px 18px; text-align: left }
        .price-table tbody tr:nth-child(even) { background: var(--accent-bg) }
        .price-table tbody td { padding: 12px 18px; border-bottom: 1px solid var(--rule) }
        .price-table .amount { font-family: "Montserrat", sans-serif; font-weight: 700; font-size: 16px; color: var(--navy) }

        .annex-section { margin: 60px 0 0; padding-top: 48px; border-top: 2px solid var(--rule) }
        .annex-tag { display: inline-block; background: var(--gold); color: #fff; font-family: "Montserrat", sans-serif; font-size: 9px; font-weight: 700; letter-spacing: 3px; text-transform: uppercase; padding: 4px 14px; margin-bottom: 12px; border-radius: 2px }
        .annex-section h2 { font-size: 22px; font-weight: 600; color: var(--navy); margin-bottom: 24px }

        .manual-item { margin-bottom: 28px }
        .manual-item h3 { font-family: "Montserrat", sans-serif; font-size: 11px; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase; color: var(--gold-light); margin-bottom: 10px; padding-bottom: 8px; border-bottom: 1px dashed var(--rule) }
        .manual-item p, .manual-item li { font-size: 14px; line-height: 1.75 }
        .manual-item ul { padding-left: 20px; margin-top: 8px }
        .manual-item li { margin-bottom: 5px }

        .benefits-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; margin: 16px 0 }
        .benefit-card { background: #fff; border: 1px solid var(--rule); border-top: 3px solid var(--gold); padding: 16px 18px; border-radius: 2px }
        .benefit-card .b-label { font-family: "Montserrat", sans-serif; font-size: 9px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; color: var(--muted); margin-bottom: 6px }
        .benefit-card .b-val { font-size: 20px; font-weight: 600; color: var(--navy) }
        .benefit-card .b-desc { font-size: 13px; color: var(--muted); margin-top: 4px }

        /* ── Signature blocks ── */
        .sig-block { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin: 40px 0 0; padding-top: 40px; border-top: 1px solid var(--rule) }
        .sig-party { text-align: center }
        .sig-line { border-bottom: 1px solid var(--ink); margin-bottom: 10px; height: 60px; display: flex; align-items: flex-end; justify-content: center; overflow: hidden; padding-bottom: 4px }
        .sig-line img { max-height: 54px; max-width: 220px; object-fit: contain }
        .sig-name { font-family: "Montserrat", sans-serif; font-size: 10px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; color: var(--navy) }
        .sig-role { font-size: 12px; color: var(--muted); margin-top: 4px }
        .sig-date-block { text-align: center; margin-bottom: 24px; font-size: 13px; color: var(--ink) }
        .sig-date-block strong { font-weight: 700 }

        /* ── Beneficial owner checkboxes ── */
        .beneficiary-check { display: flex; align-items: center; gap: 14px; margin-top: 10px; padding: 14px 18px; background: #fff; border: 1.5px solid var(--rule); border-radius: 4px }
        .beneficiary-check input[type=checkbox] { width: 20px; height: 20px; accent-color: var(--navy); flex-shrink: 0; cursor: pointer }
        .beneficiary-check label { font-size: 14px; color: var(--ink); line-height: 1.6; cursor: pointer }

        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin: 20px 0 }
        .form-field { border-bottom: 1px solid var(--ink); padding-bottom: 4px }
        .form-field label { display: block; font-family: "Montserrat", sans-serif; font-size: 9px; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase; color: var(--muted); margin-bottom: 6px }
        .form-field.full { grid-column: span 2 }

        .doc-footer { background: var(--navy); color: rgba(255,255,255,.4); font-family: "Montserrat", sans-serif; font-size: 10px; letter-spacing: 1px; text-align: center; padding: 24px 40px; margin-top: 80px }

        @media print {
            body { font-size: 11pt }
            .cover { -webkit-print-color-adjust: exact; print-color-adjust: exact }
            .section-head, .annex-section { page-break-before: auto }
            .no-print { display: none }
        }
    </style>
</head>

<body>
    <div class="cover">
        <div class="cover-inner">
            <div class="cover-label">Contractual Document</div>
            <h1>Distribution Agreement</h1>
            <div class="cover-subtitle">Pasaporte a tu Salud, S.A. de C.V.</div>
            <div class="cover-divider"></div>
            <div class="cover-parties">
                <div class="cover-party">
                    <h3>The Provider</h3>
                    <p>"Pasaporte a tu Salud",<br />Sociedad Anónima de Capital Variable<br />R.F.C. PTS260126KX3</p>
                </div>
                <div class="cover-party">
                    <h3>The Distributor</h3>
                    <p>Individual named<br />in Section 5 of the Cover Sheet</p>
                </div>
            </div>
        </div>
    </div>

    <div class="page">
        <div class="caratula-section" id="caratula">
            <div class="caratula-header"><h2>Contract Cover Sheet</h2></div>
            <table class="caratula-table">
                <tr><td>Section 1 — Provider Name</td><td>"Pasaporte a tu Salud", Sociedad Anónima de Capital Variable</td></tr>
                <tr><td>Section 2 — Legal Representative</td><td>Emilio Flores Cervantes</td></tr>
                <tr><td>Section 3 — Provider Address</td><td>Periférico Ecológico 3505, Col. Reserva Territorial Atlixayotl, Floor 4, Suite 412, San Andrés Cholula, Puebla, ZIP 72,820</td></tr>
                <tr><td>Section 4 — Provider Tax ID (R.F.C.)</td><td>PTS260126KX3</td></tr>
                <tr><td>Section 5 — Distributor Name</td><td><span class="badge" id="c_nombre">To be filled</span></td></tr>
                <tr><td>Section 6 — Distributor Address</td><td><span class="badge" id="c_domicilio">To be filled</span></td></tr>
                <tr><td>Section 7 — Distributor Tax ID / SSN</td><td><span class="badge" id="c_rfc">To be filled</span></td></tr>
                <tr><td>Section 8 — Franchisee Name</td><td><span class="badge" id="c_franquicia">To be filled</span></td></tr>
                <tr><td>Section 9 — Territorial Jurisdiction</td><td><span class="badge" id="c_demarcacion">To be filled</span></td></tr>
                <tr><td>Section 10 — Distribution Right Price</td><td><strong>$20,000.00 MXN</strong> (Twenty thousand pesos, VAT included)</td></tr>
                <tr><td>Section 11 — Provider Bank Account</td><td>CLABE: 042650016008666521<br />Banca Mifel, S.A., Institución de Banca Múltiple, Grupo Financiero Mifel</td></tr>
                <tr><td>Section 12 — Start Date</td><td><span class="badge" id="c_fecha_inicio">To be filled</span></td></tr>
                <tr><td>Section 13 — End Date</td><td><span class="badge" id="c_fecha_fin">To be filled</span></td></tr>
                <tr><td>Section 14 — Exhibits</td><td>Exhibits 1 through 12 (see exhibit index)</td></tr>
                <tr><td>Section 15 — Signing Date</td><td><span class="badge" id="c_fecha_firma">To be filled</span></td></tr>
            </table>
        </div>

        <div class="annexes-nav no-print">
            <h3>Exhibit Index</h3>
            <div class="annexes-grid">
                <a href="#anexo1"><span class="num">1</span> Franchise Agreement</a>
                <a href="#anexo2"><span class="num">2</span> Distributor Manual</a>
                <a href="#anexo3"><span class="num">3</span> Distributor ID</a>
                <a href="#anexo4"><span class="num">4</span> Proof of Address</a>
                <a href="#anexo5"><span class="num">5</span> Tax Status Certificate</a>
                <a href="#anexo6"><span class="num">6</span> Member Manual</a>
                <a href="#anexo7"><span class="num">7</span> PATS Operations Manual</a>
                <a href="#anexo8"><span class="num">8</span> Card Pricing</a>
                <a href="#anexo9"><span class="num">9</span> Payment Receipt</a>
                <a href="#anexo10"><span class="num">10</span> Distributor Commissions</a>
                <a href="#anexo11"><span class="num">11</span> Privacy Notice</a>
                <a href="#anexo12"><span class="num">12</span> Beneficial Owner Declaration</a>
            </div>
        </div>

        <div class="section-head">
            <div class="label">Background</div>
            <h2>Sole Recital — Franchise Agreement</h2>
        </div>
        <div class="clause-body">
            <p>Prior to the execution of this agreement, "Pasaporte a tu Salud", S.A. de C.V., acting as Franchisor, and the individual named in Section 8 of the Cover Sheet, acting as Franchisee, entered into a Franchise Agreement granting a temporary and non-exclusive license to manage, offer, promote, and sell the Discount Card commercially known as "Pasaporte a tu Salud".</p>
            <div class="callout info"><strong>Product Description</strong>A benefits program that provides discounts and access to high-quality private medical services in an accessible, clear, and unrestricted manner within the 50 (Fifty) Doctors Hospital Network, based in the State of Puebla, Mexico.</div>
            <p>For such purposes, the Franchisee will form a Distributor Network to carry out, in the field and within the designated territory, the promotion and sale of the Discount Card. The Franchise Agreement is attached as <strong>Exhibit 1</strong>.</p>
        </div>

        <div class="section-head">
            <div class="label">Declarations</div>
            <h2>By the Parties</h2>
        </div>

        <div class="clause">
            <div class="clause-title"><span class="clause-num">I</span> The Provider Declares</div>
            <div class="clause-body">
                <p>A. A commercial company incorporated under Mexican law, as evidenced by public deed number 54,318, volume 500, dated January 26, 2026, before Notary Public number Twenty-nine of the Judicial District of Puebla, registered in the Commercial Registry under electronic mercantile folio N-2026007106.</p>
                <p>B. The legal representative holds all necessary powers to execute this agreement, with appointment as Sole Administrator.</p>
                <p>C–D. Tax address and R.F.C. as stated in Sections 3 and 4 of the Cover Sheet.</p>
                <p>F. It is their wish to grant The Distributor the temporary right to distribute and sell the Discount Card anywhere in Mexico, following the guidelines of the Distributor Manual and under the supervision of The Franchisee.</p>
            </div>
        </div>

        <div class="clause">
            <div class="clause-title"><span class="clause-num">II</span> The Distributor Declares</div>
            <div class="clause-body">
                <p>A–C. An individual of legal age, exercising full legal capacity, with address and Tax ID as stated in Sections 5, 6 and 7 of the Cover Sheet. All funds used to fulfill this agreement are from lawful sources.</p>
                <p>D–E. Has read the Franchise Agreement and understands the marketing structure; will act under the supervision of The Franchisee and follow the Distributor Manual.</p>
                <p>F. Receives herewith the Member Manual (Exhibit 6), which must be delivered to each Discount Card purchaser.</p>
                <p>G. Has been informed about the financial criteria for determining profit margins and receives the PATS System Operations Manual (Exhibit 7).</p>
            </div>
        </div>

        <div class="section-head">
            <div class="label">Agreement Body</div>
            <h2>Clauses</h2>
        </div>

        <div class="clause" id="clausula1">
            <div class="clause-title"><span class="clause-num">One</span> Subject Matter</div>
            <div class="clause-body">
                <p>The Provider grants The Distributor the <strong>onerous, personal and non-transferable right</strong> to distribute and sell the Discount Card to any individual or legal entity, at the price and conditions set forth in Exhibit 8.</p>
                <div class="callout warning"><strong>Important</strong>The Distributor shall act at all times under the direction and supervision of The Franchisee and shall rely exclusively on the advertising materials provided by The Provider. This right may not be assigned, transferred or sublicensed to third parties, except as provided in Clause Seventeen.</div>
                <p>The Provider may grant an equal right to any individual or legal entity of its choosing, without The Distributor's prior consent.</p>
            </div>
        </div>

        <div class="clause" id="clausula2">
            <div class="clause-title"><span class="clause-num">Two</span> Price</div>
            <div class="clause-body">
                <table class="price-table">
                    <thead><tr><th>Description</th><th>Amount</th><th>Condition</th></tr></thead>
                    <tbody><tr><td>Distribution and commercialization right</td><td><span class="amount">$20,000.00 MXN</span></td><td>VAT included · One-time payment · Non-refundable</td></tr></tbody>
                </table>
                <div class="callout danger"><strong>Non-refundable payment</strong>The payment is one-time and non-refundable, even in the event of rescission or termination of this agreement. Any payment made to a party other than The Provider will not be recognized.</div>
                <p>Payment is made via electronic wire transfer to the account stated in Section 11 of the Cover Sheet (CLABE: 042650016008666521, Banca Mifel). The payment receipt is attached as Exhibit 9.</p>
            </div>
        </div>

        <div class="clause" id="clausula3">
            <div class="clause-title"><span class="clause-num">Three</span> Obligations of The Distributor</div>
            <div class="clause-body">
                <p>During the term of this agreement, The Distributor agrees to:</p>
                <div class="obl-item"><div class="obl-key">a</div><div class="obl-val">Fulfill the subject matter of the agreement diligently, complying with the Provider's administrative policies and guidelines.</div></div>
                <div class="obl-item"><div class="obl-key">b</div><div class="obl-val">Carry out activities aimed at the sale of the Discount Card in accordance with the prices set by The Provider.</div></div>
                <div class="obl-item"><div class="obl-key">c</div><div class="obl-val"><strong>Refrain</strong> from giving interviews or running advertising without prior written authorization from The Provider.</div></div>
                <div class="obl-item"><div class="obl-key">d</div><div class="obl-val"><strong>Refrain</strong> from disclosing information about commercial conditions or the contents of the Franchise Agreement.</div></div>
                <div class="obl-item"><div class="obl-key">e</div><div class="obl-val"><strong>Refrain</strong> from making public statements that damage the reputation of The Provider, the Card, or the Franchise.</div></div>
                <div class="obl-item"><div class="obl-key">f</div><div class="obl-val"><strong>Refrain</strong> from entering into contracts on behalf of The Provider and/or The Franchisee without written authorization.</div></div>
                <div class="obl-item"><div class="obl-key">g</div><div class="obl-val"><strong>Refrain</strong> from transferring the distribution right in any form without written authorization.</div></div>
                <div class="obl-item"><div class="obl-key">h</div><div class="obl-val"><strong>Refrain</strong> from receiving funds on behalf of The Provider and/or The Franchisee.</div></div>
                <div class="obl-item"><div class="obl-key">i</div><div class="obl-val"><strong>Refrain</strong> from operating, promoting, or acquiring any business or business model similar to that of The Provider.</div></div>
                <div class="obl-item"><div class="obl-key">j</div><div class="obl-val"><strong>Refrain</strong> from using The Provider's registered trademarks without express written authorization.</div></div>
                <div class="callout warning" style="margin-top:16px"><strong>Non-Competition Clause — 5 years after termination</strong>k) Not operate, promote, or participate in any business similar to or competitive with the Discount Card's purpose.<br /><br />l) Not contact, solicit, or serve customers who purchased the Card through their activities during the term of this agreement.</div>
            </div>
        </div>

        <div class="clause" id="clausula7">
            <div class="clause-title"><span class="clause-num">Seven</span> Distributor Commission</div>
            <div class="clause-body">
                <table class="price-table">
                    <thead><tr><th>Description</th><th>Sale Price</th><th>Commission</th></tr></thead>
                    <tbody><tr><td>Discount Card (direct sale by The Distributor)</td><td><span class="amount">$800.00 MXN</span></td><td><span class="amount">$20.00 MXN</span> per card paid to The Provider</td></tr></tbody>
                </table>
            </div>
        </div>

        <div class="clause" id="clausula8">
            <div class="clause-title"><span class="clause-num">Eight</span> Term</div>
            <div class="clause-body">
                <div class="benefits-grid">
                    <div class="benefit-card"><div class="b-label">Initial term</div><div class="b-val">3 years</div><div class="b-desc">From the date stated in Section 12 of the Cover Sheet</div></div>
                    <div class="benefit-card"><div class="b-label">Renewal — request period</div><div class="b-val">90 days</div><div class="b-desc">Before expiration, in writing with acknowledgment of receipt. At The Provider's discretion.</div></div>
                </div>
            </div>
        </div>

        <div class="clause" id="clausula12">
            <div class="clause-title"><span class="clause-num">Twelve</span> Liquidated Damages</div>
            <div class="clause-body">
                <div class="callout danger"><strong>Penalty for Breach</strong>In the event of a breach of any obligation under this agreement, The Provider shall have the right to demand, in addition to rescission and any other legal remedy, liquidated damages equivalent to <strong>40% of the price agreed in Clause Two</strong>, i.e., 40% of $20,000.00 MXN.</div>
            </div>
        </div>

        <div class="clause" id="clausula24">
            <div class="clause-title"><span class="clause-num">Twenty-Four</span> Jurisdiction</div>
            <div class="clause-body">
                <div class="callout info"><strong>Governing Law and Jurisdiction</strong>The Parties expressly submit to the jurisdiction and laws of the competent courts of the <strong>Judicial District of Puebla, State of Puebla, Mexico</strong>, waiving any other jurisdiction that may correspond to them.</div>
            </div>
        </div>

        {{-- ══ MAIN SIGNATURE BLOCK ══ --}}
        <div class="sig-date-block" style="margin-top:60px;">
            <p>H. Puebla de Zaragoza on <strong id="sig_dia">___</strong> of <strong id="sig_mes">_______________</strong> of the year <strong id="sig_anio">_______</strong></p>
        </div>
        <div class="sig-block">
            <div class="sig-party">
                <div class="sig-line"></div>
                <div class="sig-name">"Pasaporte a tu Salud", S.A. de C.V.</div>
                <div class="sig-role">Represented by Emilio Flores Cervantes<br />Sole Administrator</div>
            </div>
            <div class="sig-party">
                <div class="sig-line">
                    <img id="sig_firma_img" src="" alt="Distributor signature" style="display:none;">
                </div>
                <div class="sig-name" id="sig_nombre_dist">The Distributor</div>
                <div class="sig-role">Individual named in Section 5 of the Cover Sheet</div>
            </div>
        </div>

        {{-- EXHIBITS --}}
        <div class="annex-section" id="anexo1">
            <span class="annex-tag">Exhibit 1</span>
            <h2>Franchise Agreement</h2>
            <div class="callout info"><strong>Note</strong>Simple copy of the Franchise Agreement described in the Sole Recital of this instrument. Attached to the body of the agreement as a separate document.</div>
        </div>

        <div class="annex-section" id="anexo2">
            <span class="annex-tag">Exhibit 2</span>
            <h2>Distributor Manual</h2>
            <div class="manual-item">
                <h3>Initial Investment</h3>
                <table class="price-table">
                    <thead><tr><th>Description</th><th>Amount</th><th>Condition</th></tr></thead>
                    <tbody><tr><td>Distribution license</td><td><span class="amount">$20,000.00 MXN</span></td><td>VAT included · One-time · Non-refundable</td></tr></tbody>
                </table>
            </div>
            <div class="manual-item">
                <h3>Commissions</h3>
                <table class="price-table">
                    <thead><tr><th>Product</th><th>Sale Price</th><th>Distributor Commission</th></tr></thead>
                    <tbody><tr><td>Discount Card</td><td><span class="amount">$800.00 MXN</span></td><td><span class="amount">$20.00 MXN</span> per card paid to The Provider</td></tr></tbody>
                </table>
            </div>
        </div>

        <div class="annex-section" id="anexo8">
            <span class="annex-tag">Exhibit 8</span>
            <h2>Discount Card Pricing</h2>
            <table class="price-table">
                <thead><tr><th>Product</th><th>Sale Price</th></tr></thead>
                <tbody>
                    <tr><td>Distribution License</td><td><span class="amount">$20,000.00 MXN</span> VAT included</td></tr>
                    <tr><td>Pasaporte a tu Salud ("The Discount Card")</td><td><span class="amount">$800.00 MXN</span> VAT included</td></tr>
                </tbody>
            </table>
            <div class="sig-date-block" style="margin-top:40px;">
                <p>H. Puebla de Zaragoza on <strong id="anexo8_dia">___</strong> of <strong id="anexo8_mes">_______________</strong> of the year <strong id="anexo8_anio">_______</strong></p>
            </div>
            <div class="sig-block">
                <div class="sig-party">
                    <div class="sig-line"></div>
                    <div class="sig-name">"Pasaporte a tu Salud", S.A. de C.V.</div>
                    <div class="sig-role">Emilio Flores Cervantes — Sole Administrator</div>
                </div>
                <div class="sig-party">
                    <div class="sig-line">
                        <img id="anexo8_firma_img" src="" alt="Distributor signature" style="display:none;">
                    </div>
                    <div class="sig-name" id="anexo8_nombre_dist">The Distributor</div>
                    <div class="sig-role">Section 5 of the Cover Sheet</div>
                </div>
            </div>
        </div>

        <div class="annex-section" id="anexo10">
            <span class="annex-tag">Exhibit 10</span>
            <h2>Commissions to be Earned by The Distributor</h2>
            <table class="price-table">
                <thead><tr><th>Product</th><th>Sale Price</th><th>Commission</th></tr></thead>
                <tbody><tr><td>Direct sale of Discount Card by The Distributor</td><td><span class="amount">$800.00 MXN</span> VAT included</td><td><span class="amount">$20.00 MXN</span> VAT included<br />per card paid to The Provider</td></tr></tbody>
            </table>
            <div class="sig-date-block" style="margin-top:40px;">
                <p>H. Puebla de Zaragoza on <strong id="anexo10_dia">___</strong> of <strong id="anexo10_mes">_______________</strong> of the year <strong id="anexo10_anio">_______</strong></p>
            </div>
            <div class="sig-block">
                <div class="sig-party">
                    <div class="sig-line"></div>
                    <div class="sig-name">"Pasaporte a tu Salud", S.A. de C.V.</div>
                    <div class="sig-role">Emilio Flores Cervantes — Sole Administrator</div>
                </div>
                <div class="sig-party">
                    <div class="sig-line">
                        <img id="anexo10_firma_img" src="" alt="Distributor signature" style="display:none;">
                    </div>
                    <div class="sig-name" id="anexo10_nombre_dist">The Distributor</div>
                    <div class="sig-role">Section 5 of the Cover Sheet</div>
                </div>
            </div>
        </div>

        <div class="annex-section" id="anexo12">
            <span class="annex-tag">Exhibit 12</span>
            <h2>Beneficial Owner Declaration</h2>
            <p style="font-size:11px;font-family:'Montserrat',sans-serif;letter-spacing:.5px;color:var(--muted);margin-bottom:24px;">
                Pursuant to applicable anti-money laundering regulations regarding the identification of beneficial owners and controlling beneficial owners.
            </p>
            <div style="background:#fff;border:1px solid var(--rule);border-top:3px solid var(--navy);padding:28px 28px 32px;border-radius:2px;">
                <div style="font-family:'Montserrat',sans-serif;font-size:9px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--navy);margin-bottom:20px;">
                    Customer Identification Data
                </div>
                <div class="form-grid">
                    <div class="form-field full"><label>1. Last name(s) and first name(s)</label><span class="badge" id="c_apellidos_nombre">To be filled</span></div>
                    <div class="form-field"><label>2. Date of birth</label><span class="badge" id="c_fecha_nacimiento">To be filled</span></div>
                    <div class="form-field"><label>3. Country of birth</label><span class="badge" id="c_pais_nacimiento">To be filled</span></div>
                    <div class="form-field"><label>7. Phone number(s)</label><span class="badge" id="c_telefono_12">To be filled</span></div>
                    <div class="form-field"><label>8. Email address</label><span class="badge" id="c_correo_12">To be filled</span></div>
                    <div class="form-field"><label>10. Tax ID / SSN / ITIN</label><span class="badge" id="c_rfc_12">To be filled</span></div>
                </div>

                {{-- ── DECLARATION 18 — radios that notify parent via postMessage ── --}}
                <div style="margin-top:24px;padding:18px 22px;background:#f5f5f5;border-left:4px solid #111;border-radius:2px;">
                    <div style="font-family:'Montserrat',sans-serif;font-size:10px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;margin-bottom:10px;">
                        18. Beneficial Owner Declaration <span style="color:#cc0000;">*</span>
                    </div>
                    <p style="font-size:14px;line-height:1.7;margin-bottom:16px;">
                        I declare that I am the direct beneficiary of this transaction and that there is no other beneficial owner or controlling beneficial owner.
                    </p>

                    <div id="ben_aviso" style="display:flex;align-items:center;gap:8px;padding:10px 14px;background:#fffbeb;border:1.5px solid #d97706;border-radius:4px;font-size:13px;color:#92400e;margin-bottom:14px;">
                        <span>⚠</span> Please select an option — this field is required to continue.
                    </div>

                    <div class="beneficiary-check">
                        <input type="radio" id="check_beneficiario_si" name="beneficiario_directo" value="SI">
                        <label for="check_beneficiario_si"><strong>Yes</strong> — I am the sole and direct beneficial owner of this transaction</label>
                    </div>
                    <div class="beneficiary-check" style="margin-top:8px;">
                        <input type="radio" id="check_beneficiario_no" name="beneficiario_directo" value="NO">
                        <label for="check_beneficiario_no"><strong>No</strong> — There is another beneficial owner or controlling beneficial owner</label>
                    </div>

                    <div id="ben_ok" style="display:none;margin-top:12px;padding:10px 14px;background:#ecfdf5;border:1.5px solid #10b981;border-radius:4px;font-size:13px;color:#065f46;">
                        ✓ <span id="ben_ok_txt"></span>
                    </div>
                </div>

                {{-- ── Exhibit 12 signature block ── --}}
                <div style="margin-top:32px;text-align:center;">
                    <p style="font-size:13px;margin-bottom:20px;">
                        H. Puebla de Zaragoza on <strong id="anexo12_dia">___</strong> of
                        <strong id="anexo12_mes">_______________</strong> of the year
                        <strong id="anexo12_anio">_______</strong>
                    </p>
                    <div style="display:inline-block;width:300px;">
                        <div style="border-bottom:1px solid var(--ink);height:60px;margin-bottom:8px;display:flex;align-items:flex-end;justify-content:center;overflow:hidden;padding-bottom:4px;">
                            <img id="anexo12_firma_img" src="" alt="Distributor signature" style="display:none;max-height:54px;max-width:260px;object-fit:contain;">
                        </div>
                        <div style="font-family:'Montserrat',sans-serif;font-size:10px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--navy);" id="anexo12_nombre_dist">Name and Signature</div>
                        <div style="font-size:12px;color:var(--muted);margin-top:4px;">Beneficial Owner</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="doc-footer">Pasaporte a tu Salud, S.A. de C.V. · R.F.C. PTS260126KX3 · Document generated for internal use</div>

    <script>
    (function () {
        // ── 1. Auto-fill today's date in all signature blocks ─────────────────
        const hoy   = new Date();
        const dia   = hoy.getDate();
        const months = ['January','February','March','April','May','June',
                         'July','August','September','October','November','December'];
        const mes   = months[hoy.getMonth()];
        const anio  = hoy.getFullYear();

        ['sig','anexo8','anexo10','anexo12'].forEach(id => {
            const d = document.getElementById(id + '_dia');
            const m = document.getElementById(id + '_mes');
            const a = document.getElementById(id + '_anio');
            if (d) d.textContent = dia;
            if (m) m.textContent = mes;
            if (a) a.textContent = anio;
        });

        // ── 2. Read signature and name — sessionStorage first, URL params as fallback ──
        // The parent page (solicitud_distribuidor) saves to sessionStorage before
        // reloading the iframe, so the contract picks it up on load.
        let firmaData = '';
        let nombre    = '';

        try {
            firmaData = sessionStorage.getItem('pats_firma')  || '';
            nombre    = sessionStorage.getItem('pats_nombre') || '';
        } catch (_) {}

        if (!firmaData) {
            const params = new URLSearchParams(window.location.search);
            firmaData = params.get('firma')  || '';
            nombre    = params.get('nombre') || '';
        }

        // ── 3. Inject signature into all distributor blocks ───────────────────
        if (firmaData) {
            ['sig_firma_img','anexo8_firma_img','anexo10_firma_img','anexo12_firma_img'].forEach(imgId => {
                const img = document.getElementById(imgId);
                if (img) { img.src = firmaData; img.style.display = ''; }
            });
        }

        if (nombre) {
            ['sig_nombre_dist','anexo8_nombre_dist','anexo10_nombre_dist','anexo12_nombre_dist'].forEach(nomId => {
                const el = document.getElementById(nomId);
                if (el) el.textContent = nombre;
            });
        }

        // ── 4. Radios — notify parent via postMessage (same origin) ─────────────
        const radios   = document.querySelectorAll('input[name="beneficiario_directo"]');
        const aviso    = document.getElementById('ben_aviso');
        const benOk    = document.getElementById('ben_ok');
        const benOkTxt = document.getElementById('ben_ok_txt');

        const textos = {
            SI: 'Declaration confirmed: you are the sole direct beneficial owner.',
            NO: 'You indicated that another beneficial owner or controlling beneficial owner exists.',
        };

        radios.forEach(radio => {
            radio.addEventListener('change', () => {
                const val = radio.value;

                // Visual feedback inside the contract
                if (aviso) aviso.style.display = 'none';
                if (benOk) {
                    benOk.style.display = '';
                    benOk.style.borderColor = val === 'SI' ? '#10b981' : '#ef4444';
                    benOk.style.background  = val === 'SI' ? '#ecfdf5'  : '#fff1f2';
                    benOk.style.color       = val === 'SI' ? '#065f46'  : '#991b1b';
                }
                if (benOkTxt) benOkTxt.textContent = textos[val] || '';

                // ── Notify parent (solicitud_distribuidor) via postMessage ──
                // Same origin — window.parent works without CORS restrictions.
                try {
                    window.parent.postMessage(
                        { type: 'pats_beneficiario', valor: val },
                        window.location.origin
                    );
                } catch (_) {}
            });
        });

        // Pre-select if already saved in sessionStorage
        try {
            const prevVal = sessionStorage.getItem('pats_beneficiario');
            if (prevVal) {
                const prevRadio = document.querySelector('input[name="beneficiario_directo"][value="' + prevVal + '"]');
                if (prevRadio) { prevRadio.checked = true; prevRadio.dispatchEvent(new Event('change')); }
            }
        } catch (_) {}

    })();
    </script>
</body>
</html>