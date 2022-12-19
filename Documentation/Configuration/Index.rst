.. include:: ../Includes.txt

.. _configuration:

=============
Configuration
=============

Install extension
===============

1. Active extension
# Usage

2. Add this on TSConfig file
.. code-block:: typoscript
    lbo_backend_filters {
        filters {
            <table name> {
                <filter name> {
                    size = <small | medium | large>
                    label = <filter label>
                    fieldname = <column name on table>
                }
            }
        }
    }

### Exemple
.. code-block:: typoscript
lbo_backend_filters {
    filters {
        tx_lboextension_domain_model_modelName {
            code {
                size = small
                label = Code
                fieldname = code
            }
            name {
                size = medium
                label = Name
                fieldname = name
            }
        }
    }
}

