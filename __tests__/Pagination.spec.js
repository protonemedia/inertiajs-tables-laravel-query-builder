import { mount } from "@vue/test-utils"
import BasePagination from "../js/Components/Pagination.vue";
import Pagination from "../js/Tailwind2/Pagination.vue";
import expect from 'expect'

describe('Pagination.vue', () => {
    it('uses the default translations', () => {
        const component = mount(Pagination, {
            propsData: {
                meta: {
                    total: 0
                },
            }
        });

        expect(component.html()).toContain("No results found");
    });

    it('can use custom translations', () => {
        BasePagination.setTranslations({
            no_results_found: "Geen resultaten gevonden"
        })

        const component = mount(Pagination, {
            propsData: {
                meta: {
                    total: 0
                },
            }
        });

        expect(component.html()).toContain("Geen resultaten gevonden");
    });

})