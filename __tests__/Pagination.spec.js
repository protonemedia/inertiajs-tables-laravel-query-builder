import { mount } from "@vue/test-utils"
import BasePagination from "../js/Components/Pagination.vue";
import Pagination from "../js/Tailwind2/Pagination.vue";
import expect from 'expect'

describe('Pagination.vue', () => {
    it('uses the default translations', () => {
        const component = mount(Pagination, {
            propsData: {
                meta: {
                    total: 0,
                    to: 0,
                    from: 0,
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
                    total: 0,
                    to: 0,
                    from: 0,
                },
            }
        });

        expect(component.html()).toContain("Geen resultaten gevonden");
    });

    it('works with a json eloquent collection', () => {
        const component = mount(Pagination, {
            propsData: {
                meta: {
                    current_page: 7,
                    data: [],
                    first_page_url: "http://project.test/resource?page=1",
                    from: 61,
                    last_page: 10,
                    last_page_url: "http://project.test/resource?page=10",
                    links: Array[12],
                    next_page_url: "http://project.test/resource?page=8",
                    path: "http://project.test/resource",
                    per_page: 10,
                    prev_page_url: "http://project.test/resource?page=6",
                    to: 70,
                    total: 0, // for test only
                },
            }
        });

        expect(component.vm.previousPageUrl).toContain("http://project.test/resource?page=6");
        expect(component.vm.nextPageUrl).toContain("http://project.test/resource?page=8");
    });

    it('works with a json resource collection', () => {
        const component = mount(Pagination, {
            propsData: {
                meta: {
                    data: [],
                    links: {
                        first: "http://project.test/resource?page=1",
                        last: "http://project.test/resource?page=10",
                        next: "http://project.test/resource?page=7",
                        prev: "http://project.test/resource?page=5",
                    },
                    meta: {
                        current_page: 6,
                        from: 51,
                        last_page: 10,
                        links: [],
                        path: "http://shop.test/admin/customers",
                        per_page: 10,
                        to: 60,
                        total: 0, // for test only
                    },
                },
            }
        });

        expect(component.vm.previousPageUrl).toContain("http://project.test/resource?page=5");
        expect(component.vm.nextPageUrl).toContain("http://project.test/resource?page=7");
    });

})