describe('The Home Page', function() {
    it('successfully loads', function() {
        cy.visit('/', {
            auth: {
                username: 'cpi',
                password: 'sunshin3w33k',
            },
        });
    });

    it('opens hamburger menu', function() {
        cy.get('.js-nav-open').click();

        cy.get('.nav--active').should('be.visible');
    });
});
