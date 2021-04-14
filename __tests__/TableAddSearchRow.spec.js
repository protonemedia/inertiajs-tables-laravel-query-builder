import { mount } from "@vue/test-utils"
import TableAddSearchRow from "../js/Tailwind2/TableAddSearchRow.vue";
import expect from 'expect'

describe('TableAddSearchRow.vue', () => {
    it('renders a button for each search row', () => {
        let newRows = [];

        const component = mount(TableAddSearchRow, {
            propsData: {
                rows: {
                    name: {
                        key: "name",
                        label: "Name",
                        value: null
                    },

                    email: {
                        key: "email",
                        label: "Email",
                        value: null
                    }
                },

                onAdd(key) {
                    newRows.push(key)
                }
            }
        });

        const buttons = component.findAll("button[role='menuitem']");
        expect(buttons).toHaveLength(2);

        buttons.at(0).trigger('click');

        expect(newRows).toHaveLength(1);
        expect(newRows[0]).toEqual("name");
    });

    it('hides the button when the only row is the global search', () => {
        const component = mount(TableAddSearchRow, {
            propsData: {
                rows: {
                    global: {
                        key: "global",
                        label: "global",
                        value: null,
                    },
                },

                onAdd() { }
            }
        });

        const dropdownButton = component.find("button[aria-haspopup='true']");
        expect(dropdownButton.exists()).toBeFalsy();
    });

    it('hides the rows where a value is set', () => {
        const component = mount(TableAddSearchRow, {
            propsData: {
                rows: {
                    name: {
                        key: "name",
                        label: "Name",
                        value: null,
                        enabled: false
                    },

                    email: {
                        key: "email",
                        label: "Email",
                        value: "@protone.media",
                        enabled: true
                    }
                },

                onAdd() { }
            }
        });

        // click on dropdown
        const dropdownButton = component.find("button[aria-haspopup='true']");
        dropdownButton.trigger('click');

        component.vm.$nextTick(() => {
            const buttons = component.findAll("button[role='menuitem']");
            expect(buttons).toHaveLength(2);

            expect(buttons.at(0).isVisible()).toBeTruthy();
            expect(buttons.at(1).isVisible()).toBeFalsy();
        });
    });
})