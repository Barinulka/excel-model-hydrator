import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['item', 'summary', 'name', 'description', 'updated', 'scenarios', 'period', 'modelLink'];

    connect() {
        if (!this.hasSummaryTarget) {
            return;
        }

        const activeItem = this.itemTargets.find((item) => item.classList.contains('project-list__item_active'));
        if (activeItem) {
            this.updateSummary(activeItem);
        }
    }

    select(event) {
        const item = event.currentTarget;

        this.itemTargets.forEach((node) => {
            node.classList.remove('project-list__item_active');
        });
        item.classList.add('project-list__item_active');

        this.updateSummary(item);
    }

    updateSummary(item) {
        if (!this.hasSummaryTarget) {
            return;
        }

        this.nameTarget.textContent = item.dataset.projectName || '';
        this.descriptionTarget.textContent = item.dataset.projectDescription || '';
        this.updatedTarget.textContent = item.dataset.projectUpdated || '';
        this.scenariosTarget.textContent = item.dataset.projectScenarios || '';
        this.periodTarget.textContent = item.dataset.projectPeriod || '';
        this.modelLinkTarget.setAttribute('href', item.dataset.projectModelUrl || '#');
    }
}
