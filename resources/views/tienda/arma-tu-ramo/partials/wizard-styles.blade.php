<style>
/* ============ WIZARD GLOBAL STYLES ============ */
.arma-ramo-wizard {
    min-height: calc(100vh - 200px);
    padding-bottom: 100px;
}

.wizard-header {
    padding-top: 20px;
}

.wizard-title {
    font-family: var(--font-serif);
    font-size: 2.5rem;
    font-weight: 600;
    color: var(--flores-text);
    margin-bottom: 8px;
}

.wizard-subtitle {
    font-size: 1.1rem;
    color: var(--flores-text-light);
}

.wizard-content {
    max-width: 1200px;
    margin: 0 auto;
}

.paso-header {
    margin-bottom: 32px;
}

.paso-numero {
    display: inline-block;
    background: var(--flores-primary);
    color: #fff;
    padding: 4px 16px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 12px;
}

.paso-titulo {
    font-family: var(--font-serif);
    font-size: 2rem;
    font-weight: 600;
    color: var(--flores-text);
    margin-bottom: 8px;
}

.paso-descripcion {
    font-size: 1rem;
    color: var(--flores-text-light);
}

/* ============ WIZARD ACTIONS ============ */
.wizard-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    max-width: 600px;
    margin: 40px auto 0;
    padding-top: 24px;
    border-top: 1px solid var(--flores-border);
}

.wizard-actions .btn {
    min-width: 160px;
    padding: 12px 24px;
    font-weight: 500;
    border-radius: 8px;
}

.wizard-actions .btn-primary {
    background: var(--flores-primary);
    border-color: var(--flores-primary);
}

.wizard-actions .btn-primary:hover:not(:disabled) {
    background: var(--flores-primary-hover);
    border-color: var(--flores-primary-hover);
}

.wizard-actions .btn-primary:disabled {
    background: #ccc;
    border-color: #ccc;
    cursor: not-allowed;
}

/* ============ RESUMEN FLOTANTE ============ */
.resumen-flotante {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: #fff;
    border-top: 1px solid var(--flores-border);
    box-shadow: 0 -4px 20px rgba(0,0,0,0.1);
    padding: 16px 0;
    z-index: 1000;
}

.resumen-flotante-inner {
    display: flex;
    justify-content: space-between;
    align-items: center;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

.resumen-info {
    display: flex;
    align-items: center;
    gap: 24px;
}

.resumen-item {
    display: flex;
    flex-direction: column;
}

.resumen-label {
    font-size: 0.75rem;
    color: var(--flores-text-light);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.resumen-value {
    font-weight: 600;
    color: var(--flores-text);
}

.resumen-total-value {
    font-size: 1.5rem;
    font-weight: 700;
}

@media (max-width: 768px) {
    .wizard-title {
        font-size: 1.8rem;
    }

    .paso-titulo {
        font-size: 1.5rem;
    }

    .wizard-actions {
        flex-direction: column;
        gap: 12px;
    }

    .wizard-actions .btn {
        width: 100%;
    }

    .resumen-flotante-inner {
        flex-direction: column;
        gap: 12px;
        text-align: center;
    }

    .resumen-info {
        flex-wrap: wrap;
        justify-content: center;
    }
}
</style>
