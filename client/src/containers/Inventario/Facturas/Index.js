import React from 'react'
import Typography from '@material-ui/core/Typography';
import Card from '@material-ui/core/Card';
import CardActions from '@material-ui/core/CardActions';
import AddIcon from '@material-ui/icons/Add';
import SearchIcon from '@material-ui/icons/Search';
import Button from '@material-ui/core/Button';
import TextField from '@material-ui/core/TextField';
import CardContent from '@material-ui/core/CardContent';
import InputAdornment from '@material-ui/core/InputAdornment';
import DesktopWindowsIcon from '@material-ui/icons/DesktopWindows';
import EqualizerIcon from '@material-ui/icons/Equalizer';
import Avatar from '@material-ui/core/Avatar';
import Initializer from '../../../store/Initializer'
import Confirmar from '../../../components/Confirmar'

import { LocalizationTable, TableIcons, removeAccent } from '../../../utils/table.js'
import MaterialTable from "material-table";
import { Grid } from '@material-ui/core';
import { obtenerTodos,anularFactura} from '../../../utils/API/facturas';
import Crear from './componentes/Crear'
import Eliminar from './componentes/Eliminar'
import Filtro from './componentes/Filtro'

export default function Sistemas(props) {
    const initializer = React.useContext(Initializer);

    const [data, setData] = React.useState([])
    const [open, setOpen] = React.useState(false)
    const [open2, setOpen2] = React.useState(false)
    const [selected, setSelected] = React.useState(null)
    const [selected2, setSelected2] = React.useState(null)
    const [openFilter, setOpenFilter] = React.useState(false)
    const [confirmarMensaje, setConfirmarMensaje] = React.useState(false)

    React.useEffect(() => {
        if (initializer.usuario != null) {
            obtenerTodos(setData, initializer)
        }
    }, [initializer.usuario])
    const carga = () => {
        obtenerTodos(setData, initializer)
        setSelected(null)
        setSelected2(null)
    }
    const anular=()=>{
        anularFactura(selected2.id,initializer,carga)

    }
    return (
        <Grid container spacing={2}>
            <Crear sistema={selected} setSelected={setSelected} setOpen={setOpen} open={open} carga={carga} />
            <Eliminar sistema={selected2} setOpen={setOpen2} open={open2} carga={carga} />
            <Filtro setOpen={setOpenFilter} open={openFilter}  />
            <Confirmar open={confirmarMensaje} setOpen={setConfirmarMensaje} accion={anular} titulo='¿Desea continuar? Se anulará la factura.'/>

            <Grid item xs={12} md={12} style={{display:'flex',justifyContent:'space-between'}}>
                <Typography variant="h5" >
                    Facturas
                </Typography>
                <Button onClick={() => setOpen(true)} startIcon={<AddIcon />} variant="contained" color="primary">
                        Nuevo
                    </Button>
            </Grid>

            <Grid item xs={12} md={12} style={{ display: 'flex', marginTop: 10 }}>

                <Card style={{ width: 300, height: 120, marginRight: 20, marginBottom: 5,borderRadius:12,borderColor: 'rgba(0, 0, 0, 0.12)',borderWidth:1,borderStyle: 'solid'}} elevation={0}>
                    <CardContent>
                        <Typography variant="subtitle1" gutterBottom>
                            Totales
                        </Typography>
                        <div style={{ display: 'flex', justifyContent: 'space-between' }}>
                            <Typography variant="h4" gutterBottom>
                                {data.length}
                            </Typography>
                            <Avatar variant="rounded" style={{ backgroundColor: 'rgb(94, 53, 177)', borderRadius: 20 }} >
                                <DesktopWindowsIcon />
                            </Avatar>
                        </div>
                    </CardContent>
                </Card>
               
            </Grid>
      
            <Grid item xs={12}>
                <MaterialTable
                    icons={TableIcons}
                    columns={[
                       
                        { title: "Factura #", field: "id" },
                        { title: "Estado", field: "status",
                        render: rowData => (
                            <span >{rowData.status=='A'?'Completada':'Anulada'}</span>
                        )  },
                        { title: "Documento", field: "document",
                        render: rowData => (
                            <span >{rowData.final_consumer==1?'-':rowData.document}</span>
                        )  },
                        { title: "Cliente", field: "names",
                        render: rowData => (
                            <span >{rowData.final_consumer==1?'Consumidor Final':rowData.names}</span>
                        ) },
                     
                        { title: "Total", field: "total" },
                        { title: "IVA", field: "iva" },

                        
                        { title: "Registro", field: "created_at", type: "datetime" },


                    ]}
                    data={
                        data
                    }

                    localization={LocalizationTable}
                    actions={[
                        {
                            icon: TableIcons.Delete,
                            tooltip: 'Anular',

                            onClick: (event, rowData) => {
                                if(rowData.status!='C'){
                                    setConfirmarMensaje(true)
                                    setSelected2(rowData)
                                }else{
                                    initializer.mostrarNotificacion({ type: "warning", message: 'La factura ya está anulada' });

                                }
                          
                            }
                        },

                    ]}

                    options={{
                        pageSize:10,
                        showTitle: false,
                        actionsColumnIndex: -1,
                      
                        maxBodyHeight: 350,
                        padding: 'dense',
                        headerStyle: {
                            textAlign: 'left'
                        },
                        cellStyle: {
                            textAlign: 'left'
                        },
                        searchFieldStyle: {

                            padding: 5
                        }
                    }}

                />
            </Grid>
        </Grid>
    )
}
