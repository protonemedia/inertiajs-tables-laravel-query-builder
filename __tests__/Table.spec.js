import { mount } from "@vue/test-utils"
import Table from "../js/Tailwind2/Table.vue";
import expect from 'expect'

describe('Table.vue', () => {
    it('can enable a search row', () => {
        let updates = [];

        const searchProp = {
            name: {
                key: "name",
                label: "Name",
                value: null,
                enabled: false
            },
        };

        const component = mount(Table, {
            propsData: {
                search: searchProp,

                onUpdate(data) {
                    updates.push(data)
                }
            }
        });

        expect(component.vm.queryBuilderData.search).toEqual(searchProp);
        expect(component.vm.newSearch).toHaveLength(0);

        component.vm.enableSearch("name");

        expect(component.vm.newSearch).toHaveLength(1);
        expect(component.vm.newSearch[0]).toBe("name")

        expect(component.vm.queryBuilderData.search.name.enabled).toBeTruthy();

        component.vm.$nextTick(() => {
            expect(updates).toHaveLength(1);
        });
    });

    it('can disable a search row', () => {
        let updates = [];

        const searchProp = {
            name: {
                key: "name",
                label: "Name",
                value: "pro",
                enabled: true
            },
        };

        const component = mount(Table, {
            propsData: {
                search: searchProp,

                onUpdate(data) {
                    updates.push(data)
                }
            }
        });

        component.vm.newSearch.push("name");

        component.vm.disableSearch("name");

        expect(component.vm.newSearch).toHaveLength(0);
        expect(component.vm.queryBuilderData.search.name.value).toBeNull();
        expect(component.vm.queryBuilderData.search.name.enabled).toBeFalsy();

        component.vm.$nextTick(() => {
            expect(updates).toHaveLength(1);
        });
    });

    it('can change the value of a a search row', () => {
        let updates = [];

        const searchProp = {
            name: {
                key: "name",
                label: "Name",
                value: null,
                enabled: true,
            },
        };

        const component = mount(Table, {
            propsData: {
                search: searchProp,

                onUpdate(data) {
                    updates.push(data)
                }
            }
        });

        component.vm.changeSearchValue("name", "pro");

        expect(component.vm.queryBuilderData.search.name.value).toBe("pro");

        component.vm.$nextTick(() => {
            expect(updates).toHaveLength(1);
        });
    });

    it('can change the value of a the global search input', () => {
        let updates = [];

        const searchProp = {
            global: {
                key: "global",
                label: "global",
                value: null
            },
        };

        const component = mount(Table, {
            propsData: {
                search: searchProp,

                onUpdate(data) {
                    updates.push(data)
                }
            }
        });

        component.vm.changeGlobalSearchValue("pro");

        expect(component.vm.queryBuilderData.search.global.value).toBe("pro");

        component.vm.$nextTick(() => {
            expect(updates).toHaveLength(1);
        });
    });

    it('can change the value of a filter', () => {
        let updates = [];

        const filterProp = {
            name: {
                key: "name",
                label: "Name",
                value: null,
                options: { '': '-', 'a': 'Option A' }
            },
        };

        const component = mount(Table, {
            propsData: {
                filters: filterProp,

                onUpdate(data) {
                    updates.push(data)
                }
            }
        });

        expect(component.vm.queryBuilderData.filters).toEqual(filterProp);

        component.vm.changeFilterValue("name", "a");

        expect(component.vm.queryBuilderData.filters.name.value).toBe("a");

        component.vm.$nextTick(() => {
            expect(updates).toHaveLength(1);
        });
    });

    it('can toggle a column', () => {
        let updates = [];

        const columnsProp = {
            name: {
                key: "name",
                label: "Name",
                enabled: true,
            },
        };

        const component = mount(Table, {
            propsData: {
                columns: columnsProp,

                onUpdate(data) {
                    updates.push(data)
                }
            }
        });

        expect(component.vm.queryBuilderData.columns).toEqual(columnsProp);

        component.vm.changeColumnStatus("name", false);

        expect(component.vm.queryBuilderData.columns.name.enabled).toBeFalsy();

        component.vm.$nextTick(() => {
            expect(updates).toHaveLength(1);
        });
    });
})