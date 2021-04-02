import { mount } from "@vue/test-utils"
import TableSearchRows from "../js/Tailwind2/TableSearchRows.vue";
import expect from 'expect'

describe('TableSearchRows.vue', () => {
    it('renders an input for each enabled search row', () => {
        let changes = [];

        const component = mount(TableSearchRows, {
            propsData: {
                rows: {
                    name: {
                        key: "name",
                        label: "Name",
                        value: null,
                        enabled: true
                    },

                    email: {
                        key: "email",
                        label: "Email",
                        value: null,
                        enabled: false
                    }
                },

                onChange(key, value) {
                    changes.push({ key, value })
                },

                onRemove() { }
            }
        });

        expect(component.vm.hasFiltersEnabled).toBeTruthy()
        expect(component.vm.enabledFilters).toHaveLength(1)

        const input = component.find("input");

        input.setValue("pro")

        expect(changes).toHaveLength(1);

        expect(changes[0]).toEqual({
            key: "name", value: "pro"
        })
    });


    it('can remove a row', () => {
        let removals = [];

        const component = mount(TableSearchRows, {
            propsData: {
                rows: {
                    name: {
                        key: "name",
                        label: "Name",
                        value: null,
                        enabled: true
                    },

                    email: {
                        key: "email",
                        label: "Email",
                        value: null,
                        enabled: false
                    }
                },

                onChange() { },

                onRemove(key) { removals.push(key) }
            }
        });

        const button = component.find('button');
        button.trigger('click');

        expect(removals).toHaveLength(1);
        expect(removals[0]).toEqual("name")
    });
})